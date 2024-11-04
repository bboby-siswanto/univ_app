<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Devs extends App_core
// class Devs extends Api_core
// class Devs extends MX_Controller
{
    public $listdir;
    function __construct()
    {
        parent::__construct();
		$this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('devs/Devs_model', 'Dm');
        $this->load->model('academic/Subject_model', 'Sbm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('finance/Invoice_model', 'Im');
        $this->load->model('finance/Finance_model', 'Fm');
        $this->load->model('finance/Bni_model', 'Bnim');
        $this->load->model('personal_data/Family_model', 'Fmm');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->load->model('thesis/Thesis_model', 'Tsm');
        $this->load->model('validation_requirement/validation_requirement_model', 'Vm');
        $this->load->model('study_program/Study_program_model', 'Spm');

        $this->listdir = [];
    }

    function test_lms() {
        // $url = 'https://lms.iuli.ac.id/webservice/rest/server.php';
        $this->load->library('IULI_Lms');
        $list_course = $this->iuli_lms->execute('core_course_get_courses');
        // $a_param_data = [
        //     'wstoken' => '50d76340cc29f18caed255cc46e1850b',
        //     'wsfunction' => 'core_course_get_courses',
        //     'moodlewsrestformat' => 'json'
        // ];
        print('<pre>');var_dump($list_course);exit;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        // //     'Content-Type: application/json'
        // // ));
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $a_param_data);

        // curl_setopt($ch, CURLOPT_URL, 'https://lms.iuli.ac.id/webservice/rest/server.php');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $curl_exec = curl_exec($ch);
        // $result = json_decode($curl_exec);
        // curl_close($ch);

        // // $curl = curl_init();

        // // curl_setopt_array($curl, array(
        // //     CURLOPT_URL => 'https://lms.iuli.ac.id/webservice/rest/server.php',
        // //     CURLOPT_RETURNTRANSFER => true,
        // //     CURLOPT_ENCODING => '',
        // //     CURLOPT_MAXREDIRS => 10,
        // //     CURLOPT_TIMEOUT => 0,
        // //     CURLOPT_FOLLOWLOCATION => true,
        // //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // //     CURLOPT_CUSTOMREQUEST => 'POST',
        // //     CURLOPT_POSTFIELDS => $a_param_data,
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);

        // print('<pre>');var_dump($curl_exec);exit;
    }

    public function testtime()
    {
        $time = '2023-07-19 15:46:10';
        // $s_student_number = '11202007003';
        // $md = md5($s_student_number.(strtotime($time) * 1000));
        $md = date('Y-m-d', strtotime($time." +1 month"));
        print($md);exit;
    }

    function script_list() {
        $maindir = APPPATH;
        $allparam = '';
        $subdir = '';
        if (!empty($this->input->get('tp'))) {
            $subdir = $this->input->get('tp');
            $allparam = (empty($allparam)) ? $subdir : $allparam.'/'.$subdir;
        }
        $maindir .= $allparam;
        $listing = (is_dir($maindir)) ? scandir($maindir) : false;

        if (!empty($this->input->get('sc'))) {
            $listing = $this->_find_script($maindir, $this->input->get('sc'));
            if (isset($this->a_page_data['listing_finder'])) {
                $listing = $this->listdir;
            }
        }
        // print('<pre>');var_dump($this->listdir);exit;
        $this->a_page_data['subdir'] = $subdir;
        $this->a_page_data['sc'] = $this->input->get('sc');;
        $this->a_page_data['lastpath'] = $maindir;
        $this->a_page_data['listing'] = $listing;
        $this->a_page_data['body'] = $this->load->view('devs/list_files', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function force_updatebilling()
    {
        $a_billing_data = [
            'trx_id' => '43557392',
            'trx_amount' => '1000000',
            'customer_name' => 'Chiara Farahangiz Samandari',
            'datetime_expired' => '2024-10-15 23:59:59',
            'description' => 'Graduation Fee 2024',
        ];
        $a_bni_result = $this->Bnim->update_billing($a_billing_data);
        print("<pre>");var_dump($a_bni_result);exit;
    }

    function forlap_student_body() {
        $this->load->library('FeederAPI', ['mode' => 'production']);
        $s_student_number = ['11201501002','11201501003','11201501004','11201501005','11201501006','11201501008','11201501009','11201501010','11201501011','11201501012','11201501013','11201501014','11201501016','11201501017','11201501018','11201501019','11201501020','11201501021','11201501022','11201501023','11201501024','11201501025','11201501026','11201501027','11201501028','11201501029','11201501030','11201501031','11201501032','11201501034','11201501035','11201501036','11201502001','11201502002','11201502003','11201502004','11201502005','11201502006','11201502008','11201502009','11201502010','11201502010','11201502011','11201502012','11201502014','11201502015','11201502016','11201502020','11201502021','11201502022','11201502023','11201502024','11201502026','11201502027','11201502028','11201502031','11201502033','11201502034','11201502035','11201502036','11201502037','11201502038','11201502039','11201502040','11201502041','11201502042','11201502043','11201502044','11201502045','11201502046','11201503001','11201503001','11201503002','11201503002','11201503003','11201503004','11201503005','11201503006','11201504002','11201504003','11201504004','11201504005','11201504006','11201504007','11201504008','11201505001','11201505002','11201505003','11201505004','11201505005','11201505006','11201505007','11201506001','11201506002','11201506003','11201506004','11201506006','11201507001','11201507003','11201507004','11201507005','11201507006','11201507007','11201507008','11201507009','11201507011','11201507012','11201507013','11201507014','11201507015','11201507016','11201507017','11201507018','11201507019','11201507020','11201507022','11201507023','11201507024','11201508001','11201508002','11201508003','11201508004','11201508005','11201508006','11201508007','11201508008','11201508010','11201508011','11201508012','11201508013','11201508014','11201508015','11201508016','11201508017','11201508018','11201508019','11201509001','11201509002','11201509003','11201509004','11201509005','11201509006','11201509007','11201509008','11201509009','11201509010','11201509018','11201510001','11201510002','11201510003','11201510004','11201510005','11201510006','11201510007','11201510008','11201510009','11201510010','11201510011','11201510012','11201510013','11201510014','11201510015','11201510016','11201510017','11201601001','11201601002','11201601005','11201601006','11201601008','11201601009','11201601010','11201601011','11201601012','11201601013','11201601014','11201601016','11201601017','11201601018','11201601019','11201601020','11201601021','11201601022','11201601023','11201601024','11201601025','11201601027','11201602001','11201602002','11201602003','11201602004','11201602005','11201602006','11201602007','11201602008','11201602009','11201602010','11201602011','11201602012','11201602013','11201602014','11201602015','11201602016','11201602017','11201602018','11201602019','11201602021','11201602022','11201602023','11201602024','11201602025','11201602027','11201602029','11201602030','11201602031','11201602032','11201602033','11201603001','11201603003','11201603004','11201603005','11201603006','11201603007','11201603008','11201604003','11201604004','11201604006','11201603007','11201604008','11201604009','11201605002','11201605003','11201606001','11201606002','11201606003','11201606004','11201606006','11201602033','11201607001','11201607002','11201607003','11201607005','11201607006','11201607007','11201607008','11201607009','11201607010','11201607011','11201607012','11201607014','11201607015','11201607016','11201607017','11201607018','11201608001','11201608002','11201608004','11201608005','11201608007','11201608008','11201608009','11201609001','11201609002','11201609003','11201609004','11201610001','11201610002','11201610003','11201610004','11201610005','11201610006','11201610007','11201610008','11201610008','11201616001','11201701001','11201701002','11201701003','11201701004','11201701005','11201701006','11201701007','11201701008','11201701009','11201701010','11201701011','11201701012','11201701013','11201701014','11201701015','11201701016','11201701017','11201701018','11201701019','11201701020','11201701021','11201702001','11201702002','11201702003','11201702004','11201702005','11201702006','11201702007','11201702008','11201702009','11201702010','11201702011','11201702012','11201702013','11201702014','11201702015','11201702016','11201702017','11201702018','11201702019','11201702020','11201702021','11201702022','11201703001','11201703002','11201703003','11201703004','11201703005','11201703006','11201703007','11201703008','11201704001','11201704002','11201704003','11201704004','11201704005','11201704006','11201704007','11201705001','11201705002','11201705003','11201705004','11201705005','11201705006','11201706001','11201706002','11201706003','11201706004','11201706005','11201706006','11201707001','11201707002','11201707003','11201707004','11201707005','11201707006','11201707007','11201707008','11201707009','11201707010','11201707011','11201707012','11201708002','11201708003','11201708004','11201708005','11201708006','11201708007','11201708008','11201708009','11201708011','11201708012','11201709001','11201710002','11201710003','11201710004','11201710005','11201711001','11201711006','11201711007','11201712001','11201712002','11201712003','11201712004','11201712005','11201712006','11201712007','11201713001','11201713002','11201713003','11201713004','11201713004','11201801001','11201801002','11201801005','11201801006','11201801007','11201801010','11201801012','11201801035','11201801014','11201801015','11201801016','11201801020','11201801022','11201801023','11201801024','11201801025','11201801026','11201801027','11201801028','11201801029','11201801030','11201801031','11201802001','11201802003','11201802004','11201802005','11201802006','11201802007','11201802008','11201802009','11201802010','11201802011','11201802012','11201802013','11201802014','11201802015','11201802016','11201802017','11201802018','11201802019','11201802020','11201803003','11201803004','11201803006','11201803007','11201803008','11201803010','11201803011','11201803012','11201804001','11201804002','11201804003','11201805001','11201805003','11201805004','11201806001','11201806002','11201806003','11201806004','11201806005','11201806006','11201806007','11201806009','11201807001','11201807002','11201807003','11201807004','11201807005','11201807006','11201807007','11201807008','11201807009','11201807010','11201807011','11201807012','11201807013','11201807014','11201807015','11201807016','11201807017','11201808001','11201808003','11201808004','11201808005','11201808006','11201808007','11201808008','11201808009','11201808010','11201808011','11201808013','11201808015','11201808016','11201808017','11201808018','11201808019','11201809002','11201809003','11201810001','11201810003','11201810004','11201810005','11201811001','11201812001','11201812002','11201812003','11201812005','11201812006','11201812007','11201813001','11201813002','11201813003','11201813004','11201901001','11201901002','11201901004','11201901006','11201901007','11201901008','11201901009','11201901010','11201901011','11201901013','11201901015','11201901016','11201901018','11201901019','11201901020','11201901021','11201901022','11201902001','11201902004','11201902005','11201902007','11201902008','11201902009','11201902011','11201902012','11201902013','11201902014','11201902015','11201903001','11201903003','11201903004','11201903005','11201903006','11201903008','11201904002','11201904003','11201904004','11201904005','11201904006','11201904007','11201905001','11201905002','11201906001','11201906002','11201906003','11201906004','11201906005','11201907001','11201907002','11201907003','11201907004','11201907005','11201907007','11201907008','1201907002','11201907010','11201907012','11201907013','11201907014','11201907015','11201908002','11201908003','11201908004','11201908005','11201909001','11201909002','11201909003','11201909004','11201909005','11201909006','11201909007','11201909009','11201910001','11201910002','11201910003','11201910004','11201910005','11201910007','11201912005','11201912006','11201912009','11201912010','11201912011','11202001001','11202001002','11202001003','11202001004','11202001005','11202001006','11202001007','11202003009','11202001016','11202001017','11202001018','11202001019','11202002001','11202002004','11202002005','11202002006','11202002011','11202002013','11202002015','11202002021','11202002018','11202003001','11202003003','11202003004','11202003005','11202003007','11202007021','11202004002','11202005002','11202005003','11202005005','11202005006','11202007001','11202007002','11202007003','11202007004','11202007005','11202007006','11202007007','11202007008','11202007010','11202007012','11202007013','11202007015','11202007017','11202007018','11202007020','11202008002','11202008008','11202008009','11202008010','11202008011','11202008012','11202009001','11202009003','11202009004','11202009006','11202010002','11202012001','11202012003','11202012005','11202012012','11202101001','11202101002','11202101003','11202101004','11202101005','11202101006','11202101007','11202101008','11202101009','11202101010','11202101011','11202101012','11202101013','11202102002','11202102003','11202102004','11202102005','11202102006','11202102007','11202102008','11202102009','11202102010','11202102011','11202102012','11202102013','11202102014','11202102015','11202102016','11202104001','11202106002','11202106003','11202106004','11202106006','11202107002','11202107006','11202107007','11202109001','11202110001','11202110002','11202110003','11202110005','11202110006','11202110007','11202112001','11202112002','11202201001','11202201002','11202201003','11202201004','11202201005','11202201006','11202201008','11202201009','11202201010','11202201011','11202202001','11202202002','11202202003','11202202005','11202202007','11202202009','11202202011','11202202012','11202202013','11202202014','11202203001','11202203003','11202204001','11202204002','11202204003','11202204004','11202205001','11202205002','11202206001','11202206002','11202206004','11202207001','11202207002','11202207003','11202207004','11202207005','11202208002','11202208003','11202208004','11202208005','11202208008','11202208009','11202208010','11202209002','11202209003','11202209005','11202209006','11202210001','11202210002','11202210003','11202212002','11202212004','11202308001','11202308003','11202310001','11202310002'];
        print('<table border="1">');
        print('<tr>');
        print('<th>No</th>');
        print('<th>Nama</th>');
        print('<th>NIM</th>');
        print('<th>JK</th>');
        print('<th>Pekerjaan Orang Tua</th>');
        print('<th>Asal Sekolah</th>');
        print('<th>Alamat Negara</th>');
        print('<th>Provinsi</th>');
        print('<th>Kota</th>');
        print('<th>Kelurahan</th>');
        print('<th>Kecamatan</th>');
        print('</tr>');
        
        $i_number = 1;
        foreach ($s_student_number as $s_student_number) {
            $mba_student_filtered = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number]);
            if (!$mba_student_filtered) {
                // $mba_student_filtered = $this->Stm->get_student_filtered(['dpd.personal_data_name' => ]);
                // print($i_number++.'. ');
                print($s_student_number.' not found!');
                print('<br>');
            }
            $o_student = $mba_student_filtered[0];

            $parent_job = '';
            $o_wilayah = false;
            $mba_student_institution = $this->Inm->get_student_institution(['st.student_id' => $o_student->student_id, 'ri.institution_type' => 'highschool']);
            $mba_student_family = $this->Fmm->get_family_by_personal_data_id($o_student->personal_data_id);
            if ($mba_student_family) {
                $mba_family_data = $this->Fmm->get_family_lists_filtered(['fmm.family_id' => $mba_student_family->family_id, 'family_member_status != ' => 'child']);
                if ($mba_family_data) {
                    foreach ($mba_family_data as $o_parent) {
                        if (!is_null($o_parent->ocupation_name)) {
                            $parent_job = $o_parent->ocupation_name;
                        }
                    }
                }
            }
            
            if (!is_null($o_student->dikti_wilayah_id)) {
                $mba_diktiwilayah = $this->feederapi->post('GetWilayah', [
                    'filter' => "id_wilayah = '$o_student->dikti_wilayah_id'"
                ]);
                if (($mba_diktiwilayah->error_code == 0) AND (count($mba_diktiwilayah->data) > 0)) {
                    $o_wilayah = $mba_diktiwilayah->data[0];
                    if ($o_wilayah->id_level_wilayah == 3) {
                        $mba_diktiwilayah = $this->feederapi->post('GetWilayah', [
                            'filter' => "id_wilayah = '$o_wilayah->id_induk_wilayah'"
                        ]);
                        if (($mba_diktiwilayah->error_code == 0) AND (count($mba_diktiwilayah->data) > 0)) {
                            $o_wilayah = $mba_diktiwilayah->data[0];
                        }
                    }
                }
            }
            $country = false;
            if (!is_null($o_student->address_country_id)) {
                $country = $this->General->get_where('ref_country', ['country_id' => $o_student->address_country_id]);
            }
            print('<tr>');
            print('<td>'.$i_number++.'</td>');
            print('<td>'.$o_student->personal_data_name.'</td>');
            print('<td>'.$o_student->student_number.'</td>');
            print('<td>'.(($o_student->personal_data_gender == 'M') ? 'Laki-Laki' : (($o_student->personal_data_gender == 'F') ? 'Perempuan' : '')).'</td>');
            print('<td>'.$parent_job.'</td>');
            print('<td>'.(($mba_student_institution) ? $mba_student_institution[0]->institution_name : '').'</td>');
            print('<td>'.(($country) ? $country[0]->country_name : '').'</td>');
            print('<td>'.$o_student->address_province.'</td>');
            print('<td>'.(($o_wilayah) ? $o_wilayah->nama_wilayah : $o_student->address_city).'</td>');
            print('<td>'.$o_student->address_district.'</td>');
            print('<td>'.$o_student->address_sub_district.'</td>');
            print('</tr>');
        }
        print('</table>');
    }

    private function _find_script($s_dir, $s_stringfind = false) {
        $a_exept_find = ['.', '..', '...', 'uploads', 'vendor', 'vendor.old', 'logs', 'cache'];
        $a_extension_allowed = ['php'];
        $list_dir = scandir($s_dir);

        $this->a_page_data['listing_finder'] = [];
        
        foreach ($list_dir as $s_directory) {
            if (!in_array($s_directory, $a_exept_find)) {
                $s_newpath = $s_dir.$s_directory.'/';
                if (is_dir($s_newpath)) {
                    $this->_find_script($s_newpath, $s_stringfind);
                }
                else {
                    $s_newpath = $s_dir.$s_directory;
                    if (file_exists($s_newpath)) {
                        $a_file = explode('.', $s_directory);
                        $s_extension = $a_file[count($a_file) - 1];
                        if (in_array($s_extension, $a_extension_allowed)) {
                            $content = file_get_contents($s_newpath);
                            $finder = strpos($content, $s_stringfind);
                            if ($finder !== false) {
                                $public_path = str_replace(APPPATH, 'portal/', $s_dir);
                                array_push($this->listdir, $public_path.$s_directory);
                                // print($s_newpath);
                                // print('<br>');
                            }
                        }
                    }
                    
                }
            }
        }
    }

    public function fill_transcript_sign()
    {
        exit;
        $this->logdb = $this->load->database('dblog', true);
        $mba_transcript_data = $this->General->get_where('dt_student_document_token', ['document_type' => 'transcript_halfway']);
        if ($mba_transcript_data) {
            foreach ($mba_transcript_data as $o_transcript) {
                $date = $o_transcript->date_added;
                $prefix = (date('Y-m-d', strtotime($date)) >= date('Y-m-d', strtotime('2021-05-30'))) ? 'apps_log_' : 'dt_access_log_';
                $table_date = date('Ym', strtotime($date));
                $table = $prefix.$table_date;
                // print($table);exit;
                $this->logdb->from($table);
                $this->logdb->where('access_log_method', 'generate_transcript_halfway');
                $this->logdb->like('access_log_timestamp', date('Y-m-d H:i', strtotime($date)));
                $query = $this->logdb->get();
                if ($query->num_rows() == 0) {
                    $tday = date('t', strtotime($date));
                    if (date('d', strtotime($date)) >= ($tday - 2)) {
                        $nextdate = date('Y-m-d H:i:s', strtotime($date." +1 month"));
                        $nextprefix = (date('Y-m-d', strtotime($nextdate)) >= date('Y-m-d', strtotime('2021-05-30'))) ? 'apps_log_' : 'dt_access_log_';
                        $nexttable_date = date('Ym', strtotime($nextdate));
                        $nexttable = $nextprefix.$nexttable_date;

                        $this->logdb->from($nexttable);
                        $this->logdb->where('access_log_method', 'generate_transcript_halfway');
                        $this->logdb->like('access_log_timestamp', date('Y-m-d H:i', strtotime($date)));
                        $query = $this->logdb->get();
                        if ($query->num_rows() == 0) {
                            print('<pre>');var_dump($o_transcript);
                            print('<br>'.$nexttable);exit;
                        }
                    }
                }
            }
        }
    }

    function get_list_function($s_module, $s_classname = false) 
    {
        $s_module = strtolower($s_module);
        $s_classname = ucfirst(strtolower($s_classname));
        $targetpath = APPPATH.'modules/'.$s_module.'/controllers/'.$s_classname.'.php';
        if (!$s_classname) {
            $s_module = ucfirst(strtolower($s_module));
            $targetpath = APPPATH.'controllers/'.$s_module.'.php';
        }
        // print($targetpath);exit;
        if (file_exists($targetpath)) {
            $page_controller = file_get_contents($targetpath);
            $start_line = strpos($page_controller, 'function __construct');
            $page_controllers = substr($page_controller, $start_line, -3);
            $a_line_controllers = explode("\n", $page_controllers);

            $a_page_list = array();
            $a_start_function_struct = ['function', 'public', 'private'];
            foreach($a_line_controllers as $key => $line) {
                if ($key > 0) {
                    $line = trim($line);
                    $a_line_word = explode(' ', trim($line));
                    if (in_array($a_line_word[0], $a_start_function_struct)) {
                        $diff_last_function_name = (trim($a_line_word[count($a_line_word) - 1]) == '{') ? 2 : 1;
                        $idx_start_function_name = (in_array($a_line_word[1], $a_start_function_struct)) ? 2 : 1;
                        $idx_last_function_name = count($a_line_word) - $diff_last_function_name;

                        $a_function_name = [];
                        for ($i=$idx_start_function_name; $i <= $idx_last_function_name ; $i++) { 
                            array_push($a_function_name, $a_line_word[$i]);
                        }
                        $s_function_name = implode(' ', $a_function_name);
                        // print('<pre>');var_dump($s_function_name);exit;
                        // $s_function_name = str_replace('(', '', str_replace(')', '', $a_line_word[$idx_start_function_name]));
                        // $s_function_name = explode('$', $s_function_name)[0];

                        // if ((strpos($s_function_name, 'gp_') === FALSE)
                        //     AND (strpos($s_function_name, 'validasi_') === FALSE)
                        //     AND (strpos($s_function_name, 'sp_') === FALSE)
                        //     AND (strpos($s_function_name, 'siswa_') === FALSE)) {
                            array_push($a_page_list, $s_function_name);
                        // }
                        
                    }
                }
            }

            print('<li>'.implode('</li><li>', $a_page_list).'</li>');
        }
        else {
            show_404();
        }
    }

    function invoice_installment() {
        $mba_unpaid_invoice = $this->Im->get_invoice_by_deadline([
            'fee.payment_type_code' => '02',
            'di.academic_year_id' => 2023,
            'di.semester_type_id' => 1
        ]);
        // print('<pre>');var_dump($mba_unpaid_invoice);exit;
        if ($mba_unpaid_invoice) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'tuition_fee_2023_1_(Installment_October_2023)';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/report/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet()->setTitle("Tuition Fee Report");
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Study Program");
            $o_sheet->setCellValue('C1', "Batch");
            $o_sheet->setCellValue('D1', "Student Number");
            $o_sheet->setCellValue('E1', "Student Status");
            $o_sheet->setCellValue('F1', "Semester Bill");
            $o_sheet->setCellValue('G1', "Invoice Amount");
            $o_sheet->setCellValue('H1', "Payment Due Date");
            $o_sheet->setCellValue('I1', "Actual Due Date");
            $o_sheet->setCellValue('J1', "Billing Description");
            // $o_sheet->setCellValue('J1', "Batch");

            $i_row = 2;
            foreach ($mba_unpaid_invoice as $o_invoice) {
                $mba_semeste_data = $this->General->get_where('ref_semester', ['semester_id' => $o_invoice->semester_id]);
                $mba_student_data = $this->Stm->get_student_filtered([
                    'ds.personal_data_id' => $o_invoice->personal_data_id,
                ], ['active', 'inactive', 'graduated']);
                if ($mba_student_data) {
                    $o_student = $mba_student_data[0];
                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                    $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                    // $o_invoice_next_billing = false;
                    
                    // if ($mba_invoice_data) {
                    //     $o_invoice_next_billing = $mba_invoice_data[0];
                    // }

                    $a_invoice_details = [];
                    $d_amount_unpaid = 0;
                    if ($mba_invoice_installment) {
                        $mba_invoice_detail_data = $this->Im->get_invoice_data([
                            'di.invoice_id' => $o_invoice->invoice_id,
                            'dsi.sub_invoice_type' => 'installment',
                            // 'dsid.sub_invoice_details_deadline' => '2023-10-10 23:59:59',
                            'dsid.sub_invoice_details_amount_paid' => 0,
                            'dsid.sub_invoice_details_status != ' => 'paid'
                        ]);
                        if ($mba_invoice_detail_data) {
                            foreach ($mba_invoice_detail_data as $o_installment) {
                                if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                    $d_amount_unpaid += $o_installment->sub_invoice_details_amount_total;
                                    array_push($a_invoice_details, $o_installment);
                                }
                            }
                        }
                    }
                    else if ($mba_invoice_fullpayment) {
                        // if ($mba_invoice_fullpayment->sub_invoice_details_deadline == '2023-10-10 23:59:59') {
                            if (($mba_invoice_fullpayment->sub_invoice_details_status != 'paid') AND ($mba_invoice_fullpayment->sub_invoice_details_amount_paid == 0)) {
                                $d_amount_unpaid += $mba_invoice_fullpayment->sub_invoice_details_amount_total;
                                array_push($a_invoice_details, $mba_invoice_fullpayment);
                            }
                        // }
                    }

                    if (count($a_invoice_details) > 0) {
                        foreach ($a_invoice_details as $o_sub_detail) {
                            $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                            $o_sheet->setCellValue('B'.$i_row, $o_student->study_program_abbreviation);
                            $o_sheet->setCellValue('C'.$i_row, $o_student->academic_year_id);
                            $o_sheet->setCellValue('D'.$i_row, $o_student->student_number);
                            $o_sheet->setCellValue('E'.$i_row, $o_student->student_status);
                            $o_sheet->setCellValue('F'.$i_row, $mba_semeste_data[0]->semester_number);
                            $o_sheet->setCellValue('G'.$i_row, $o_sub_detail->sub_invoice_details_amount_total);
                            $o_sheet->setCellValue('H'.$i_row, $o_sub_detail->sub_invoice_details_deadline);
                            $o_sheet->setCellValue('I'.$i_row, $o_sub_detail->sub_invoice_details_real_datetime_deadline);
                            $o_sheet->setCellValue('J'.$i_row, $o_sub_detail->sub_invoice_details_description);
                            // $o_sheet->setCellValue('K'.$i_row, $o_invoice->payment_type_code);
                            // $o_sheet->setCellValue('L'.$i_row, $o_invoice->invoice_status);
                            $i_row++;
                        }
                    }
                }
            }

            // print('<pre>');var_dump($a_invoice_details);exit;

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    function invoice_tuitionfee() {
        // $mba_unpaid_invoice = $this->Im->get_unpaid_invoice_full(false, ['02']);
        $s_datestart = '2023-10-01';
        $s_dateend = '2023-10-31';
        $mba_unpaid_invoice = $this->Im->get_invoice_by_deadline([
            'fee.payment_type_code' => '02'
        ]);
        if ($mba_unpaid_invoice) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'tuition_fee_deadline_('.$s_datestart.'_'.$s_dateend.')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/report/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet()->setTitle("Tuition Fee Report");
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Study Program");
            $o_sheet->setCellValue('C1', "Batch");
            $o_sheet->setCellValue('D1', "Student Status");
            $o_sheet->setCellValue('E1', "Semester Bill");
            $o_sheet->setCellValue('F1', "Invoice Amount");
            $o_sheet->setCellValue('G1', "Payment Due Date");
            $o_sheet->setCellValue('H1', "Actual Due Date");
            $o_sheet->setCellValue('I1', "Billing Description");
            // $o_sheet->setCellValue('J1', "Batch");

            $i_row = 2;
            foreach ($mba_unpaid_invoice as $o_invoice) {
                $mba_semeste_data = $this->General->get_where('ref_semester', ['semester_id' => $o_invoice->semester_id]);
                $mba_student_data = $this->Stm->get_student_filtered([
                    'ds.personal_data_id' => $o_invoice->personal_data_id,
                ], ['active', 'inactive', 'graduated']);
                if ($mba_student_data) {
                    $o_student = $mba_student_data[0];
                    // $mba_invoice_data = $this->Im->get_invoice_data([
                    //     'di.invoice_id' => $o_invoice->invoice_id,
                    //     'dsid.sub_invoice_details_deadline >= ' => $s_datestart,
                    //     'dsid.sub_invoice_details_deadline <= ' => $s_dateend,
                    //     'dsid.sub_invoice_details_amount_paid' => 0,
                    //     'dsid.sub_invoice_details_status != ' => 'paid'
                    // ]);
                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                    $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                    // $o_invoice_next_billing = false;
                    
                    // if ($mba_invoice_data) {
                    //     $o_invoice_next_billing = $mba_invoice_data[0];
                    // }

                    $a_invoice_details = [];
                    $d_amount_unpaid = 0;
                    if ($mba_invoice_installment) {
                        $mba_invoice_detail_data = $this->Im->get_invoice_data([
                            'di.invoice_id' => $o_invoice->invoice_id,
                            'dsi.sub_invoice_type' => 'installment',
                            'dsid.sub_invoice_details_deadline >= ' => $s_datestart,
                            'dsid.sub_invoice_details_deadline <= ' => $s_dateend,
                            'dsid.sub_invoice_details_amount_paid' => 0,
                            'dsid.sub_invoice_details_status != ' => 'paid'
                        ]);
                        if ($mba_invoice_detail_data) {
                            foreach ($mba_invoice_detail_data as $o_installment) {
                                if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                    $d_amount_unpaid += $o_installment->sub_invoice_details_amount_total;
                                    array_push($a_invoice_details, $o_installment);
                                }
                            }
                        }
                    }
                    else if ($mba_invoice_fullpayment) {
                        if (($mba_invoice_fullpayment->sub_invoice_details_deadline >= $s_datestart) AND ($mba_invoice_fullpayment->sub_invoice_details_deadline <= $s_dateend)) {
                            if (($mba_invoice_fullpayment->sub_invoice_details_status != 'paid') AND ($mba_invoice_fullpayment->sub_invoice_details_amount_paid == 0)) {
                                $d_amount_unpaid += $mba_invoice_fullpayment->sub_invoice_details_amount_total;
                                array_push($a_invoice_details, $mba_invoice_fullpayment);
                            }
                        }
                    }

                    if (count($a_invoice_details) > 0) {
                        foreach ($a_invoice_details as $o_sub_detail) {
                            $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                            $o_sheet->setCellValue('B'.$i_row, $o_student->study_program_abbreviation);
                            $o_sheet->setCellValue('C'.$i_row, $o_student->academic_year_id);
                            $o_sheet->setCellValue('D'.$i_row, $o_student->student_status);
                            $o_sheet->setCellValue('E'.$i_row, $mba_semeste_data[0]->semester_number);
                            $o_sheet->setCellValue('F'.$i_row, $o_sub_detail->sub_invoice_details_amount_total);
                            $o_sheet->setCellValue('G'.$i_row, $o_sub_detail->sub_invoice_details_deadline);
                            $o_sheet->setCellValue('H'.$i_row, $o_sub_detail->sub_invoice_details_real_datetime_deadline);
                            $o_sheet->setCellValue('I'.$i_row, $o_sub_detail->sub_invoice_details_description);
                            $o_sheet->setCellValue('J'.$i_row, $o_invoice->payment_type_code);
                            $o_sheet->setCellValue('K'.$i_row, $o_invoice->invoice_status);
                            $i_row++;
                        }
                    }
                }
            }

            // print('<pre>');var_dump($a_invoice_details);exit;

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    function get_invoice_installment($s_academic_year_id, $s_semester_type_id) {
        $mba_invoice_list = $this->Im->get_invoice_by_deadline([
            'fee.payment_type_code' => '02',
            'di.academic_year_id' => $s_academic_year_id,
            'di.semester_type_id' => $s_semester_type_id,
        ]);

        if ($mba_invoice_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'tuition_fee_installment_('.$s_academic_year_id.'_'.$s_semester_type_id.')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/report/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet()->setTitle("Tuition Fee Report");
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services");

            $o_sheet->setCellValue('A1', "No");
            $o_sheet->setCellValue('B1', "Fac");
            $o_sheet->setCellValue('C1', "SP");
            $o_sheet->setCellValue('D1', "Student Name");
            $o_sheet->setCellValue('E1', "Student ID");
            $o_sheet->setCellValue('F1', "Status");
            $o_sheet->setCellValue('G1', "Batch");
            $o_sheet->setCellValue('H1', "Installment");
            $o_sheet->setCellValue('H2', "July");
            $o_sheet->setCellValue('I2', "August");
            $o_sheet->setCellValue('J2', "September");
            $o_sheet->setCellValue('K2', "October");
            $o_sheet->setCellValue('L2', "November");
            $o_sheet->setCellValue('M2', "Desember");

            $i_row = 3;
            $i_number = 1;
            foreach ($mba_invoice_list as $o_invoice) {
                $mba_student_data = $this->Stm->get_student_filtered([
                    'ds.personal_data_id' => $o_invoice->personal_data_id,
                    'ds.program_id' => '1'
                ], ['active', 'inactive', 'graduated']);
                if ($mba_student_data) {
                    $o_student = $mba_student_data[0];
                    if (in_array($o_invoice->invoice_status, ['created', 'pending'])) {
                        $o_sheet->setCellValue('A'.$i_row, $i_number++);
                        $o_sheet->setCellValue('B'.$i_row, $o_student->faculty_abbreviation);
                        $o_sheet->setCellValue('C'.$i_row, $o_student->study_program_abbreviation);
                        $o_sheet->setCellValue('D'.$i_row, $o_student->personal_data_name);
                        $o_sheet->setCellValue('E'.$i_row, $o_student->student_number);
                        $o_sheet->setCellValue('F'.$i_row, $o_student->student_status);
                        $o_sheet->setCellValue('G'.$i_row, $o_student->academic_year_id);

                        // $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                        // $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                        $mba_invoice_installment = $this->Im->get_invoice_data([
                            'di.invoice_id' => $o_invoice->invoice_id,
                            'dsi.sub_invoice_type' => 'installment'
                        ]);
                        if ($mba_invoice_installment) {
                            // print($mba_student_data[0]->personal_data_name.' ('.$mba_student_data[0]->student_status.') jumlah cicilan '.count($mba_invoice_installment));
                            foreach ($mba_invoice_installment as $o_installment) {
                                if (date('m', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)) == '07') {
                                    if (($o_installment->sub_invoice_details_amount_paid == 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                        $o_sheet->setCellValue('H'.$i_row, $o_installment->sub_invoice_details_amount_total);
                                    }
                                    else {
                                        $o_sheet->setCellValue('H'.$i_row, 0);
                                    }
                                }
                                else if (date('m', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)) == '08') {
                                    if (($o_installment->sub_invoice_details_amount_paid == 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                        $o_sheet->setCellValue('I'.$i_row, $o_installment->sub_invoice_details_amount_total);
                                    }
                                    else {
                                        $o_sheet->setCellValue('I'.$i_row, 0);
                                    }
                                }
                                else if (date('m', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)) == '09') {
                                    if (($o_installment->sub_invoice_details_amount_paid == 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                        $o_sheet->setCellValue('J'.$i_row, $o_installment->sub_invoice_details_amount_total);
                                    }
                                    else {
                                        $o_sheet->setCellValue('J'.$i_row, 0);
                                    }
                                }
                                else if (date('m', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)) == '10') {
                                    if (($o_installment->sub_invoice_details_amount_paid == 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                        $o_sheet->setCellValue('K'.$i_row, $o_installment->sub_invoice_details_amount_total);
                                    }
                                    else {
                                        $o_sheet->setCellValue('K'.$i_row, 0);
                                    }
                                }
                                else if (date('m', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)) == '11') {
                                    if (($o_installment->sub_invoice_details_amount_paid == 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                        $o_sheet->setCellValue('L'.$i_row, $o_installment->sub_invoice_details_amount_total);
                                    }
                                    else {
                                        $o_sheet->setCellValue('L'.$i_row, 0);
                                    }
                                }
                                else if (date('m', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)) == '12') {
                                    if (($o_installment->sub_invoice_details_amount_paid == 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                        $o_sheet->setCellValue('M'.$i_row, $o_installment->sub_invoice_details_amount_total);
                                    }
                                    else {
                                        $o_sheet->setCellValue('M'.$i_row, 0);
                                    }
                                }
                            }
                        }
                        // else if ($mba_invoice_fullpayment) {
                        //     print($mba_student_data[0]->personal_data_name.' ('.$mba_student_data[0]->student_status.') jumlah cicilan 1 _Full Payment');
                        // }
                        // else {
                        //     print($mba_student_data[0]->personal_data_name.' ('.$mba_student_data[0]->student_status.') tidak ditemukan');
                        // }
                        
                        // print('<br>');
                        $i_row++;
                    }
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    function get_student_krs() {
        $mba_student_data =$this->Stm->get_student_filtered(['ds.student_status' => 'active']);
        print('<table border="1" width="100%">');
        print('<tr><th>Student Name</th><th>Prodi</th><th>Batch</th><th>Total Subject</th><th>Total SKS</th></tr>');
        foreach ($mba_student_data as $o_student) {
            $mba_score_data = $this->Scm->get_score_data([
                'sc.student_id' => $o_student->student_id,
                'sc.academic_year_id' => '2023',
                'sc.semester_type_id' => '1',
                'sc.score_approval' => 'approved'
            ]);
            $total_krs = ($mba_score_data) ? count($mba_score_data) : 0;
            $total_sks = 0;
            if ($mba_score_data) {
                foreach ($mba_score_data as $o_score) {
                    $total_sks += intval($o_score->curriculum_subject_credit);
                }
            }
            print('<tr>');
            print('<td>'.$o_student->personal_data_name.'</td>');
            print('<td>'.$o_student->study_program_abbreviation.'</td>');
            print('<td>'.$o_student->academic_year_id.'</td>');
            print('<td>'.$total_krs.'</td>');
            print('<td>'.$total_sks.'</td>');
            print('</tr>');
        }
        // print('<pre>');var_dump($mba_student_data);exit;
        print('</table>');
    }

    function goes() {
        // $s_path = APPPATH."uploads/temp/temp.xlsx";
        // $o_spreadsheet = IOFactory::load("$s_path");
        // $o_sheet = $o_spreadsheet->getActiveSheet();
		
        // $i_row = 2;
        // $this->load->library('zip');
        // while ($o_sheet->getCell('D'.$i_row)->getValue() !== NULL) {
        //     $s_personal_data_id = $o_sheet->getCell('A'.$i_row)->getValue();
        //     $document_link = $o_sheet->getCell('D'.$i_row)->getValue();

        //     $s_file_path = APPPATH.'uploads/'.$s_personal_data_id.'/'.$document_link;
        //     if (file_exists($s_file_path)) {
        //         $this->zip->read_file($s_file_path);
        //     }
        //     $i_row++;
        // }

        // $this->zip->download("apa aja".'.zip');

        $this->load->library('zip');
        $mba_data = $this->General->get_where('dt_personal_data pd', ['personal_data_gender' => 'F']);
        if ($mba_data) {
            foreach ($mba_data as $o_data) {
                $mba_personal_document = $this->General->get_where('dt_personal_data_document', [
                    'personal_data_id' => $o_data->personal_data_id,
                    'document_id' => '0bde3152-5442-467a-b080-3bb0088f6bac'
                ]);

                if ($mba_personal_document) {
                    $o_personal_document = $mba_personal_document[0];

                    $s_file_path = APPPATH.'uploads/'.$o_data->personal_data_id.'/'.$o_personal_document->document_requirement_link;
                    if (file_exists($s_file_path)) {
                        $a_path_info = pathinfo($s_file_path);
			            $s_file_ext = $a_path_info['extension'];
                        $s_new_name = $o_data->personal_data_name.".".$s_file_ext;

                        $this->zip->read_file($s_file_path, $s_new_name);
                        // break;
                    }
                }
            }
        }

        $this->zip->download("file".'.zip');
    }

    function prodi_list() {
        $mba_prodi_list = $this->Spm->get_study_program(false, false);
        if ($mba_prodi_list) {
            foreach ($mba_prodi_list as $o_prodi) {
                $o_prodi->head_of_study_program_name = $this->General->retrieve_title($o_prodi->head_of_study_program_id);
                $o_prodi->deans_name = $this->General->retrieve_title($o_prodi->deans_id);
            }
        }

        $this->a_page_data['prodi_data'] = $mba_prodi_list;
        $this->a_page_data['body'] = $this->load->view('study_program/study_program_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function get_for_mimosa($s_academic_year_id = 2023, $s_semester_type_id = 1) {
        if ((empty($s_academic_year_id)) AND (empty($s_semester_type_id))) {
            redirect("academic/class_group/class_group_lists");
        }
        else {
            $a_prep_data = $this->prep_mimosa_data($s_academic_year_id, $s_semester_type_id);
            $a_student_data = array_values($a_prep_data['student_data']);
            $a_lecturer_data = array_values($a_prep_data['lecturer_data']);
            $a_room_data = array_values($a_prep_data['room_data']);
            $a_subject_data = array_values($a_prep_data['subject_data']);
            $a_collection_data = array_values($a_prep_data['collection_data']);

            $s_string = "[Header]\nVersion=7.1.9\nOriginalFile=\nSourceFile=\nFileId=\nCreated=20230913 15:03:40\nCustomer=International University Liaison Indonesia\nDepartment=Mechanical Engineering\nCopyright=� Mimosa Software Ltd.\nComment=\nDescription=\n";
            $s_string .= "\n";
            $s_string .= "[Time]\nMaxWeeks=1\nMaxDays=5\nMaxLectures=10\nDate=45187\nYYYYMMDD=20230918\nFirstWeek=1\nWeekSum=0\nDays=Monday";
            $s_string .= '$Tuesday$Wednesday$Thursday$Friday$Saturday$Sunday$';
            $s_string .= "\n\n";
            $s_string .= "[Timeperiods]\n1=08:00-08:45\n2=09:00-09:45\n3=10:00-10:45\n4=11:00-11:45\n5=12:00-12:45\n6=13:00-13:45\n7=14:00-14:45\n8=15:00-15:45\n9=16:00-16:45\n10=17:00-17:45\n";
            $s_string .= "\n";
            $s_string .= "[Weeks]\n\n[Blocks]\n1=\n";
            $s_string .= "\n[Courses]\n";
            $s_string .= 'Categories=m:Mandatory$el:Elective$ex:Extracurricular$';
            $s_string .= "\n";
            $s_string .= 'Bookings=/////$Free$Other$';
            $s_string .= "\nDefault=1\n";
            $s_string .= "\n[Components]\n";
            $s_string .= 'Categories=G:Groups$T:Teachers$R:Rooms$I:Info$P:Students$E:Equipment$O:Other$';
            $s_string .= "\n";
            $s_string .= 'Bookings=/////$Meeting$Other$';
            $s_string .= "\n";
            $s_string .= "Default=1\nNoTimetables=0;0;0;1;0;0;0;\nNoGaps=0;0;1;1;0;1;1;\nRooms=3\n";
            $s_string .= "\n[Coursedata]\n";
            $i_num = 1;
            if (count($a_subject_data) > 0) {
                foreach ($a_subject_data as $o_subject) {
                    $s_string .= $i_num++."=".$o_subject['id'].";".$o_subject['name'].";".$o_subject['idx_cat'].";".$o_subject['alokasi'].";".$o_subject['null']."\n";
                }
            }
            $s_string .= "\n";
            $s_string .= "[Componentdata]\n";
            $i_num = 1;
            if (count($a_student_data) > 0) {
                foreach ($a_student_data as $a_data) {
                    $s_string .= $i_num++."=".$a_data['id'].";".$a_data['name'].";".$a_data['idx_cat'].";".$a_data['alokasi'].";".$a_data['null']."\n";
                }
            }
            if (count($a_lecturer_data) > 0) {
                foreach ($a_lecturer_data as $a_data) {
                    $s_string .= $i_num++."=".$a_data['id'].";".$a_data['name'].";".$a_data['idx_cat'].";".$a_data['alokasi'].";".$a_data['null']."\n";
                }
            }
            if (count($a_room_data) > 0) {
                foreach ($a_room_data as $a_data) {
                    $s_string .= $i_num++."=".$a_data['id'].";".$a_data['name'].";".$a_data['idx_cat'].";".$a_data['alokasi'].";".$a_data['null']."\n";
                }
            }
            // $s_string .= "\n";
            $s_string .= "\n[Collections]\n";
            $i_num = 1;
            if (count($a_collection_data) > 0) {
                foreach ($a_collection_data as $a_data) {
                    $s_string .= $i_num++."=".$a_data['class_id'].";".$a_data['member_id']."\n";
                }
            }

            // $s_string .= "\n";
            $s_string .= "\n[Weeklectures]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Weekbookings]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Tablelectures]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Tablebookings]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Manualrooms]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Coursedates]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Tablecomments]\n";
            // $s_string .= "\n";
            $s_string .= "\n[Roles]\n";
            $s_string .= "";
            

            $s_storefile = APPPATH."uploads/temp/mimosatemplate.mxt";
            if (file_exists($s_storefile)) {
                file_put_contents($s_storefile, $s_string);
            }
            else {
                file_put_contents($s_storefile, $s_string);
            }

            $a_path_info = pathinfo($s_storefile);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename=mimosatemplate.mxt');
            readfile( $s_storefile );
            exit;

            // print('<pre>');var_dump($a_prep_data);exit;
        }
    }
    public function prep_mimosa_data($s_academic_year_id, $s_semester_type_id) {
        $a_subject_skipped = ['research semester','project research','research project','internship','thesis','nfu'];
        $a_student_data = [];
        $a_lecturer_data = [];
        $a_room_data = [];
        $a_subject_data = [];
        $a_collection_data = [];
        // Componentdata, Coursedata
        
        $mba_student_active = $this->Stm->get_student_filtered([
            'ds.student_status' => 'active'
        ]);
        $room_data = $this->General->get_where('ref_room');
        if ($room_data) {
            foreach ($room_data as $o_room) {
                $a_room_data[$o_room->id_room] = [
                    'id' => $o_room->id_room,
                    'name' => $o_room->id_room.' '.$o_room->room_name,
                    'idx_cat' => 3,
                    'alokasi' => 0,
                    'null' => 0
                ];
            }
        }
        if ($mba_student_active) {
            foreach ($mba_student_active as $o_student) {
                $key = $this->get_keyfrom_name($o_student->personal_data_name);
                if (!in_array($key, $a_student_data)) {
                    $a_student_data[$key] = [
                        'id' => $key,
                        'name' => $o_student->personal_data_name,
                        'idx_cat' => 5,
                        'alokasi' => 0,
                        'null' => 0
                    ];
                }
            }
        }

        $mba_class_data = modules::run('academic/class_group/get_class_lists', [
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);
        if ($mba_class_data['code'] == 0) {
            $mba_class_data = $mba_class_data['data'];
            // print('<pre>');var_dump($mba_class_data);exit;
            if ($mba_class_data) {
                foreach ($mba_class_data as $o_class) {
                    $mba_class_subject = $this->Cgm->get_class_master_subject(['cm.class_master_id' => $o_class->class_master_id]);
                    $mba_student_data = $this->Cgm->get_class_master_student($o_class->class_master_id, [
                        'ds.score_approval' => 'approved',
                        'st.student_status' => 'active'
                    ]);

                    if (($mba_class_subject) AND ($mba_student_data)) {
                        $o_class_subject = $mba_class_subject[0];
                        $b_allow_calculate = true;
                        foreach ($a_subject_skipped as $s_subject) {
                            if (strpos(strtolower($o_class_subject->subject_name), $s_subject) !== false) {
                                $b_allow_calculate = false;
                            }
                        }

                        if ($b_allow_calculate) {
                            $mba_lecturer_class = $this->Cgm->get_class_master_lecturer([
                                'class_master_id' => $o_class->class_master_id
                            ]);
                            
                            $s_currtype = '2';
                            if ($o_class->curriculum_subject_type == 'mandatory') {
                                $s_currtype = '1';
                            }
                            else if ($o_class->curriculum_subject_type == 'extracurricular') {
                                $s_currtype = '3';
                            }
        
                            $key_class = $o_class_subject->subject_name_code;
                            $s_class = json_encode([$o_class]);
                            $s_class = strlen(base64_encode($s_class));
                            $key_class .= '-'.$s_class;
                            $key_subject = $key_class;
                            if (array_key_exists($key_subject, $a_subject_data)) {
                                $a_subject_data[$key_subject]['alokasi'] += 30;
                            }
                            else {
                                $a_subject_data[$key_subject] = [
                                    'id' => $key_subject,
                                    'name' => $o_class_subject->subject_name,
                                    'idx_cat' => $s_currtype,
                                    'alokasi' => 0,
                                    'null' => 0
                                ];
                            }
        
                            if ($mba_lecturer_class) {
                                foreach ($mba_lecturer_class as $o_lecturer) {
                                    $s_employeekey = $this->get_keyfrom_name($o_lecturer->personal_data_name);
                                    if (array_key_exists($s_employeekey, $a_lecturer_data)) {
                                        $a_lecturer_data[$s_employeekey]['alokasi'] += 30;
                                    }
                                    else {
                                        $a_lecturer_data[$s_employeekey] = [
                                            'id' => $s_employeekey,
                                            'name' => $o_lecturer->personal_data_name,
                                            'idx_cat' => 2,
                                            'alokasi' => 30,
                                            'null' => 0
                                        ];
                                    }
    
                                    array_push($a_collection_data, [
                                        'class_id' => $key_subject,
                                        'member_id' => $s_employeekey,
                                        'member_type' => 'lecturer'
                                    ]);
                                    $a_subject_data[$key_subject]['alokasi'] += 30;
                                }
                            }
        
                            foreach ($mba_student_data as $o_studentclass) {
                                $key = $this->get_keyfrom_name($o_studentclass->personal_data_name);
                                if (array_key_exists($key, $a_student_data)) {
                                    $a_student_data[$key]['alokasi'] += 30;
                                    array_push($a_collection_data, [
                                        'class_id' => $key_subject,
                                        'member_id' => $key,
                                        'member_type' => 'student'
                                    ]);
                                    $a_subject_data[$key_subject]['alokasi'] += 30;
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'student_data' => $a_student_data,
            'lecturer_data' => $a_lecturer_data,
            'room_data' => $a_room_data,
            'subject_data' => $a_subject_data,
            'collection_data' => $a_collection_data,
        ];
    }

    function get_keyfrom_name($s_fullname) {
        $s_name = ucwords(strtolower($s_fullname));
        $a_name = explode(" ", $s_name);

        $first = $a_name[0];
        $last = $a_name[count($a_name) - 1];

        if (strlen($a_name[0]) <= 1) {
            if (count($a_name) > 1) {
                $first = $a_name[0].$a_name[1];
            }
        }

        $key = $first.$last;
        if (count($a_name) == 1) {
            $key = $first;
        }

        if (strlen($key) >= 15) {
            $first = substr($first, 0, 5);
            $last = substr($last, 0, 5);

            $key = $first.$last;
            if (count($a_name) == 1) {
                $key = $first;
            }
        }
        return $key;
    }

    public function sync_dashboard_db()
    {
        $mba_student_data = $this->General->get_where('dt_student');
        if ($mba_student_data) {
            $this->sdb = $this->load->database('superset', true);
            foreach ($mba_student_data as $o_student) {
                $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_student->personal_data_id]);
                $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $o_student->study_program_id]);
                $mba_faculty_data = false;
                if (($mba_study_program_data) AND (!is_null($mba_study_program_data[0]->faculty_id))) {
                    $mba_faculty_data = $this->General->get_where('ref_faculty', ['faculty_id' => $mba_study_program_data[0]->faculty_id]);
                }
                if (($mba_personal_data) AND (!is_null($mba_personal_data[0]->citizenship_id))) {
                    $mba_country_data = $this->General->get_where('ref_country', ['country_id' => $mba_personal_data[0]->citizenship_id]);
                }
                
                $a_new_data = [
                    'student_id' => $o_student->student_id,
                    'personal_data_name' => ($mba_personal_data) ? $mba_personal_data[0]->personal_data_name : '',
                    'student_number' => $o_student->student_number,
                    'study_program_name' => ($mba_study_program_data) ? $mba_study_program_data[0]->study_program_name : '',
                    'faculty_name' => ($mba_faculty_data) ? $mba_faculty_data[0]->faculty_name : '',
                    'academic_year_id' => $o_student->academic_year_id,
                    'student_date_enrollment' => $o_student->student_date_enrollment,
                    'graduated_year_id' => $o_student->graduated_year_id,
                    'student_status' => $o_student->student_status,
                    'student_type' => $o_student->student_type,
                    'personal_data_nationality' => ($mba_personal_data) ? $mba_personal_data[0]->personal_data_nationality : '',
                    'personal_data_gender' => ($mba_personal_data) ? $mba_personal_data[0]->personal_data_gender : '',
                    'country_name' => ($mba_country_data) ? $mba_country_data[0]->country_name : ''
                ];

                // $this->sdb->insert('student_all', $a_new_data);
            }
        }
        print('okr');
        // 
        // $mba_data = $this->sdb->get_where('student_all');
        // print('<pre>');var_dump($mba_data->result());exit;
    }

    function test_error() {
        // $this->send_notification_telegram('Finish');
        print($ds);
    }

    public function inactivated_student()
    {
        $mba_student_active_list = $this->Stm->get_student_filtered([
            'ds.student_status' => 'active',
            'ds.academic_year_id <' => '2023'
        ]);

        if ($mba_student_active_list) {
            foreach ($mba_student_active_list as $o_student) {
                $mba_score_data = $this->General->get_where('dt_score', [
                    'student_id' => $o_student->student_id,
                    'score_approval' => 'approved',
                    'academic_year_id' => 2022,
                    'semester_type_id' => '2'
                ]);

                if (!$mba_score_data) {
                    print($o_student->personal_data_name.' ('.$o_student->study_program_abbreviation.'/'.$o_student->academic_year_id.')');
                    print('<br>');
                }
            }
        }
    }

    public function upload_defense_jadwal() {
        $s_template_path = APPPATH.'uploads/templates/academic/thesis_defense/2023/thesis_schedule_2022_even_upload.xlsx';
        $o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $i_row = 2;
        while ($o_sheet->getCell('B'.$i_row)->getValue() !== NULL) {
            $s_student_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('B'.$i_row)->getValue()));
            $mba_student_filtered = $this->Stm->get_student_filtered(['dpd.personal_data_name' => $s_student_name, 'ds.student_status' => 'active']);
            if ($mba_student_filtered) {
                $o_student = $mba_student_filtered[0];
                $mba_thesis_student = $this->Tsm->get_thesis_list_by_log([
                    'ts.student_id' => $o_student->student_id,
                    'tsl.thesis_log_type' => 'work',
                    'tsl.academic_year_id' => 2022,
                    'tsl.semester_type_id' => 2
                ]);

                if ($mba_thesis_student) {
                    $o_thesis_student = $mba_thesis_student[0];
                    print($s_student_name.': '.$mba_thesis_student[0]->thesis_student_id);
                    $mba_advisor = $this->Tsm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_student_id
                    ], 'advisor', true);
                    $mba_examiner = $this->Tsm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_student_id
                    ], 'examiner');

                    if ($mba_advisor) {
                        $this->Tsm->remove_advisor_data($o_thesis_student->thesis_student_id);
                    }

                    if ($mba_advisor) {
                        $this->Tsm->force_remove_data('thesis_students_examiner', ['thesis_student_id' => $o_thesis_student->thesis_student_id]);
                    }

                    if ($o_sheet->getCell('Q'.$i_row)->getValue() !== NULL) {
                        $a_student_advisor1_data = [
                            'student_advisor_id' => $this->uuid->v4(),
                            'thesis_student_id' => $o_thesis_student->thesis_student_id,
                            'advisor_id' => $o_sheet->getCell('Q'.$i_row)->getValue(),
                            'advisor_type' => 'approved_advisor_1',
                            'advisor_section' => 'iuli_advisor'
                        ];
                        // print_r($a_student_advisor1_data);
                        $this->Tsm->submit_student_advisor($a_student_advisor1_data);
                    }

                    if ($o_sheet->getCell('R'.$i_row)->getValue() !== NULL) {
                        $a_student_advisor2_data = [
                            'student_advisor_id' => $this->uuid->v4(),
                            'thesis_student_id' => $o_thesis_student->thesis_student_id,
                            'advisor_id' => $o_sheet->getCell('R'.$i_row)->getValue(),
                            'advisor_type' => 'approved_advisor_2',
                            'advisor_section' => 'iuli_co_advisor'
                        ];
                        // print_r($a_student_advisor2_data);
                        $this->Tsm->submit_student_advisor($a_student_advisor2_data);
                    }

                    if ($o_sheet->getCell('S'.$i_row)->getValue() !== NULL) {
                        $a_student_examiner1_data = [
                            'student_examiner_id' => $this->uuid->v4(),
                            'thesis_student_id' => $o_thesis_student->thesis_student_id,
                            'advisor_id' => $o_sheet->getCell('S'.$i_row)->getValue(),
                            'examiner_type' => 'examiner_1'
                        ];
                        // print_r($a_student_examiner1_data);
                        $this->Tsm->submit_student_examiner($a_student_examiner1_data);
                    }

                    if ($o_sheet->getCell('T'.$i_row)->getValue() !== NULL) {
                        $a_student_examiner2_data = [
                            'student_examiner_id' => $this->uuid->v4(),
                            'thesis_student_id' => $o_thesis_student->thesis_student_id,
                            'advisor_id' => $o_sheet->getCell('T'.$i_row)->getValue(),
                            'examiner_type' => 'examiner_2'
                        ];
                        // print_r($a_student_examiner2_data);
                        $this->Tsm->submit_student_examiner($a_student_examiner2_data);
                    }

                    $s_time = $o_sheet->getCell('M'.$i_row)->getValue();
                    $a_time = explode('-', $s_time);
                    $s_timestart = str_replace('.', ':', $a_time[0]).':00';
                    $s_timeend = str_replace('.', ':', $a_time[1]).':00';

                    $s_date = $o_sheet->getCell('L'.$i_row)->getValue();
                    $a_date = explode('.', $s_date);
                    $s_datedata = '20'.$a_date[2].'-'.$a_date[1].'-'.$a_date[0];
                    $datedata = date('Y-m-d', strtotime($s_datedata));
                    
                    $a_defense_data = [
                        'thesis_students_id' => $o_thesis_student->thesis_student_id,
                        'thesis_defense_date' => $datedata,
                        'thesis_defense_room' => $o_sheet->getCell('N'.$i_row)->getValue(),
                        'thesis_defense_time_start' => $s_timestart,
                        'thesis_defense_time_end' => $s_timeend,
                        'academic_year_id' => 2022,
                        'semester_type_id' => 2,
                        'thesis_defense_zoom_id' => $o_sheet->getCell('O'.$i_row)->getValue(),
                        'thesis_defense_zoom_passcode' => str_replace("'", "", $o_sheet->getCell('P'.$i_row)->getValue())
                    ];
                    $mba_defense_data = $this->General->get_where('thesis_defense', [
                        'thesis_students_id' => $o_thesis_student->thesis_student_id,
                        'academic_year_id' => 2022,
                        'semester_type_id' => 2
                    ]);
                    // print_r($a_defense_data);
                    if ($mba_defense_data) {
                        $this->Tsm->update_thesis_defense($a_defense_data, $mba_defense_data[0]->thesis_defense_id);
                    }
                    else {
                        $a_defense_data['thesis_defense_id'] = $this->uuid->v4();
                        $this->Tsm->insert_thesis_defense($a_defense_data);
                        // print('insert '.$s_student_name.'-'.$o_student->student_number.'/');
                    }
                    // print($s_student_name.'-'.$o_student->student_number.'/');
                    // if ($o_sheet->getCell('T'.$i_row)->getValue() !== NULL) {
                    //     $thesis_advisor = $this->General->get_where('thesis_advisor', ['advisor_id' => $o_sheet->getCell('T'.$i_row)->getValue()]);
                    //     if ($thesis_advisor) {
                    //         print($thesis_advisor[0]->advisor_status);
                    //     }
                    //     else {
                    //         print('advisor not found!!!'.$i_row);exit;
                    //     }
                    // }
                }
                else {
                    print($s_student_name.' NOT FOUND!');
                }
                print('<br>');
            }
            else {
                print($s_student_name.' NOT FOUND!');exit;
            }

            // print('<br>');
            $i_row++;
        }
    }

    public function invoice_inactive($s_academic_year_id = 2023, $s_semester_type_id = 1)
    {
        $mba_invoice_details_data = $this->Im->get_invoice_data([
            'di.academic_year_id' => $s_academic_year_id,
            'di.semester_type_id' => $s_semester_type_id,
            'di.invoice_status != ' => 'paid',
            'dsid.sub_invoice_details_status != ' => 'paid'
        ]);

        if ($mba_invoice_details_data) {
            $i_num = 1;
            $a_invoice_detail_inactive_va = [];
            // print('<pre>');var_dump($mba_invoice_details_data);exit;
            foreach ($mba_invoice_details_data as $o_invoice_detail) {
                if (is_null($o_invoice_detail->trx_id)) {
                    print($i_num++.'. '.$o_invoice_detail->sub_invoice_details_description.'/'.$o_invoice_detail->invoice_id.'<br>');
                }
                else {
                    // print($i_num++.'. '.$o_invoice_detail->sub_invoice_details_description.' / '.$o_invoice_detail->sub_invoice_details_deadline.'<br>');
                    $check = $this->Bnim->inquiry_billing($o_invoice_detail->trx_id, true);
                    $status = 77;
                    if (($check) AND (!array_key_exists('status', $check))) {
                        $status = $check['va_status'];
                        if ($o_invoice_detail->sub_invoice_details_amount_total != $check['trx_amount']) {
                            $status = 99;
                        }
                    }

                    // print($i_num++.'. '.$o_invoice_detail->sub_invoice_details_description.'/'.$status.'<br>');

                    if ($status != 1) {
                        if ($status == 2) {
                            modules::run('finance/invoice/reactivate_billing', $o_invoice_detail->trx_id);
                        }

                        if (!in_array($o_invoice_detail->invoice_id, $a_invoice_detail_inactive_va)) {
                            array_push($a_invoice_detail_inactive_va, $o_invoice_detail->invoice_id);
                            print($i_num++.'. <a href="'.base_url().'finance/invoice/sub_invoice/'.$o_invoice_detail->invoice_id.'" target="_blank">'.$o_invoice_detail->invoice_id.'</a><br>');
                        }
                    }
                    sleep(0.5);
                }
            }
        }
    }

    public function force_activate_all_invoice() {
        print('tutup');exit;
        $mba_invoice_data = $this->Im->get_unpaid_invoice_full([
            'di.invoice_allow_reminder' => 'yes'
        ], ['02']);
        $count = 0;
        if ($mba_invoice_data) {
            foreach ($mba_invoice_data as $o_invoice) {
                $mba_student_data = $this->Stm->get_student_filtered([
                    'ds.personal_data_id' => $o_invoice->personal_data_id
                ], ['active', 'inactive', 'onleave', 'graduated']);
                if ($mba_student_data) {
                    $this->cheat_billing($o_invoice->invoice_id);
                    sleep(1);
                    print($o_invoice->personal_data_name.'/'.$mba_student_data[0]->student_status);
                    $count++;
                }
                else {
                    $a_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $o_invoice->personal_data_id]);
                    if ($a_student_data) {
                        print($o_invoice->personal_data_name.'/'.$a_student_data[0]->student_status);
                    }
                    else {
                        print($o_invoice->personal_data_name);
                    }
                }

                print('<br>');
            }
        }
        print('<h1>'.$count.'</h1>');
        // print('<pre>');var_dump($mba_invoice_data);
    }

    public function test_date()
    {
        $s_date1 = '2022-05-08 14:01:51';
        $s_date2 = '2022-05-08 14:01:51';

        $s_datestart = date('Y-m-d H:i:s', strtotime($s_date1));
        $s_dateend = date('Y-m-d H:i:s', strtotime($s_date2));

        if ($s_datestart >= $s_dateend) {
            print('qadasda');
        }
    }

    public function invoice_has_paid()
    {
        $mba_invoice_unpaid_list = $this->Im->get_invoice_list_detail([
            'di.invoice_status != ' => 'paid',
            'di.academic_year_id' => '2022',
            'di.semester_type_id' => '2'
        ]);

        if ($mba_invoice_unpaid_list) {
            $i_num = 1;
            print('<div style="font-size: 12px">');
            foreach ($mba_invoice_unpaid_list as $o_invoice) {
                $mba_student_data = $this->Stm->get_student_filtered([
                    'ds.personal_data_id' => $o_invoice->personal_data_id,
                    'ds.student_status != ' => 'resign'
                ]);
                if ($mba_student_data) {
                    $mba_invoice_details_main_data = $this->Im->student_has_invoice_data($o_invoice->personal_data_id, [
                        'di.invoice_id' => $o_invoice->invoice_id,
                        'df.fee_amount_type' => 'main'
                    ]);
    
                    $mba_invoice_details = $this->Im->get_invoice_details([
                        'did.invoice_id' => $o_invoice->invoice_id,
                    ]);
    
                    $d_billing = 0;
                    $s_fee_desc = '';
                    if (($mba_invoice_details) AND ($mba_invoice_details_main_data)) {
                        $d_billing += $mba_invoice_details_main_data->invoice_details_amount;
                        $s_fee_desc = $mba_invoice_details_main_data->fee_description;
                        foreach ($mba_invoice_details as $o_details) {
                            if ($o_details->fee_amount_type != 'main') {
                                if ($o_details->invoice_details_amount_number_type == 'percentage') {
                                    $d_amount_details = $mba_invoice_details_main_data->invoice_details_amount * $o_details->invoice_details_amount / 100;
                                    if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                        $d_billing += $d_amount_details;
                                    }
                                    else {
                                        $d_billing -= $d_amount_details;
                                    }
                                }
                                else {
                                    if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                        $d_billing += $o_details->invoice_details_amount;
                                    }
                                    else {
                                        $d_billing -= $o_details->invoice_details_amount;
                                    }
                                }
                            }
                        }
                    }
                    
                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                    $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
    
                    $s_invoice_id_target = '<a href="'.base_url().'finance/invoice/sub_invoice/'.$o_invoice->invoice_id.'" target="_blank">link invoice detail</a>';
                    $s_personal_data_id_target = '<a href="'.base_url().'finance/invoice/lists/'.$o_invoice->personal_data_id.'" target="_blank">link invoice student</a>';
    
                    if ($mba_invoice_installment) {
                        $d_total_paid = 0;
                        $d_last_paid = false;
                        
                        foreach ($mba_invoice_installment as $o_installment) {
                            if ($o_installment->sub_invoice_details_amount_paid > 0) {
                                $d_total_paid += $o_installment->sub_invoice_details_amount_paid;
                                $s_datepaid = date('Y-m-d H:i:s', strtotime($o_installment->sub_invoice_details_datetime_paid_off));

                                if ($s_datepaid >= $d_last_paid) {
                                    $d_last_paid = $s_datepaid;
                                }
                            }
                        }
                        
                        if ($mba_invoice_fullpayment) {
                            if ($mba_invoice_fullpayment->sub_invoice_details_amount_paid > 0) {
                                $d_total_paid += $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                            }
                        }
    
                        if ($d_total_paid >= $d_billing) {
                            // $a_invoice_data_update = [
                            //     'invoice_datetime_paid_off' => $d_last_paid,
                            //     'invoice_status' => 'paid',
                            //     'invoice_amount_paid' => $d_total_paid
                            // ];
                            // $this->Im->update_invoice($a_invoice_data_update, [
                            //     'invoice_id' => $o_invoice->invoice_id
                            // ]);
                            print('X~ '.$i_num++.'. '.$o_invoice->personal_data_name.' ('.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->academic_year_id.') - '.$s_fee_desc.' invoice_id: '.$s_invoice_id_target.' => (total paid:'.$d_total_paid.', billing:'.$d_billing.', last paid:'.$d_last_paid.')- personal_data_id: '.$s_personal_data_id_target);
                        }
                        else {
                            print($i_num++.'. '.$o_invoice->personal_data_name.' ('.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->academic_year_id.') - '.$s_fee_desc.' invoice_id: '.$s_invoice_id_target.' => (total paid:'.$d_total_paid.', billing:'.$d_billing.', last paid:'.$d_last_paid.')- personal_data_id: '.$s_personal_data_id_target);
                        }
                    }
                    else if ($mba_invoice_fullpayment) {
                        $d_total_paid = $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                        if ($d_total_paid >= $d_billing) {
                            print('F~ '.$i_num++.'. '.$o_invoice->personal_data_name.' ('.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->academic_year_id.') - '.$s_fee_desc.' invoice_id: '.$s_invoice_id_target.' => (total paid:'.$d_total_paid.', billing:'.$d_billing.')- personal_data_id: '.$s_personal_data_id_target);
                        }
                        else {
                            print($i_num++.'. '.$o_invoice->personal_data_name.' ('.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->academic_year_id.') - '.$s_fee_desc.' invoice_id: '.$s_invoice_id_target.' => (total paid:'.$d_total_paid.', billing:'.$d_billing.')- personal_data_id: '.$s_personal_data_id_target);
                        }
                    }
                    else {
                        print($i_num++.'. invoice '.$s_invoice_id_target.' tidak ada installment/fullpayment');
                    }
                    print('<br>');
                }
            }
            print('</div>');
        }
    }

    public function check_invoice_paid()
    {
        $mba_invoice_list = $this->Im->get_invoice_list_detail([
            'di.invoice_status' => 'paid',
            'di.academic_year_id' => '2022',
            'di.semester_type_id' => '2'
        ]);
        
        if ($mba_invoice_list) {
            $i_num = 1;
            print('<div style="font-size: 12px">');
            foreach ($mba_invoice_list as $o_invoice) {
                $mba_student_data = $this->Stm->get_student_filtered([
                    'ds.personal_data_id' => $o_invoice->personal_data_id,
                    'ds.student_status != ' => 'resign'
                ]);

                $mba_invoice_details_main_data = $this->Im->student_has_invoice_data($o_invoice->personal_data_id, [
                    'di.invoice_id' => $o_invoice->invoice_id,
                    'df.fee_amount_type' => 'main'
                ]);

                $mba_invoice_details = $this->Im->get_invoice_details([
                    'did.invoice_id' => $o_invoice->invoice_id,
                ]);

                $d_billing = 0;
                $s_fee_desc = '';
                if (($mba_invoice_details) AND ($mba_invoice_details_main_data)) {
                    $d_billing += $mba_invoice_details_main_data->invoice_details_amount;
                    $s_fee_desc = $mba_invoice_details_main_data->fee_description;
                    foreach ($mba_invoice_details as $o_details) {
                        if ($o_details->fee_amount_type != 'main') {
                            if ($o_details->invoice_details_amount_number_type == 'percentage') {
                                $d_amount_details = $mba_invoice_details_main_data->invoice_details_amount * $o_details->invoice_details_amount / 100;
                                if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                    $d_billing += $d_amount_details;
                                }
                                else {
                                    $d_billing -= $d_amount_details;
                                }
                            }
                            else {
                                if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                    $d_billing += $o_details->invoice_details_amount;
                                }
                                else {
                                    $d_billing -= $o_details->invoice_details_amount;
                                }
                            }
                        }
                    }
                }
                
                $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                $s_invoice_id_target = '<a href="'.base_url().'finance/invoice/sub_invoice/'.$o_invoice->invoice_id.'" target="_blank">link invoice detail</a>';
                $s_personal_data_id_target = '<a href="'.base_url().'finance/invoice/lists/'.$o_invoice->personal_data_id.'" target="_blank">link invoice student</a>';

                if ($mba_invoice_installment) {
                    $d_total_paid = 0;
                    foreach ($mba_invoice_installment as $o_installment) {
                        if ($o_installment->sub_invoice_details_amount_paid > 0) {
                            $d_total_paid += $o_installment->sub_invoice_details_amount_paid;
                        }
                    }
                    
                    if ($mba_invoice_fullpayment) {
                        if ($mba_invoice_fullpayment->sub_invoice_details_amount_paid > 0) {
                            $d_total_paid += $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                        }
                    }

                    if ($d_total_paid < $o_invoice->invoice_amount_paid) {
                        print('X~ '.$i_num++.'. '.$o_invoice->invoice_id.' '.$o_invoice->personal_data_name.' ('.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->academic_year_id.') - '.$s_fee_desc.' invoice_id: '.$s_invoice_id_target.' => (total paid:'.$d_total_paid.', billing:'.$d_billing.')- personal_data_id: '.$s_personal_data_id_target);
                    }
                }
                else if ($mba_invoice_fullpayment) {
                    $d_total_paid = $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                    print($i_num++.'. '.$o_invoice->invoice_id.' '.$o_invoice->personal_data_name.' ('.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->academic_year_id.') - '.$s_fee_desc.' invoice_id '.$s_invoice_id_target.' => (total paid:'.$d_total_paid.', billing:'.$d_billing.')- personal_data_id: '.$s_personal_data_id_target);
                }
                else {
                    print($i_num++.'. invoice '.$s_invoice_id_target.' tidak ada installment/fullpayment');
                }
                print('<br>');
            }
            print('</div>');
        }

        print('<pre>');var_dump($mba_invoice_list);exit;
    }

    public function get_absen()
    {
        $s_ip = '192.168.254.100';
        $Connect = fsockopen($s_ip, "80", $errno, $errstr, 1);
        print('<pre>');var_dump($Connect);exit;
    }

    public function get_list_dean_hod()
    {
        $mba_faculty = $this->General->get_where('ref_faculty');
        
        if ($mba_faculty) {
            foreach ($mba_faculty as $o_faculty) {
                $mba_prodi = $this->General->get_where('ref_study_program', ['faculty_id' => $o_faculty->faculty_id]);
                $s_deans = $this->General->retrieve_title($o_faculty->deans_id);
                print('<b>'.$o_faculty->faculty_name);
                print(' / ');
                print($s_deans);
                print('</b><br>');
                
                if ($mba_prodi) {
                    foreach ($mba_prodi as $o_prodi) {
                        $s_hod = $this->General->retrieve_title($o_prodi->head_of_study_program_id);
                        print($o_prodi->study_program_name);
                        print(' / ');
                        print($s_hod);
                        print('<br>');
                    }
                }
                print('<br>');
            }
        }
    }

    function get_dosen_forlap() {
        $this->load->library('FeederAPI', ['mode' => 'production']);
        // $mba_forlapdosen = $this->feederapi->post('GetListPenugasanDosen', [
        //     'filter' => "id_dosen = '4009403f-01ef-413c-9480-bf04b12f2e7d'"
        // ]);
        // $mba_forlapdosen = $this->feederapi->post('GetRiwayatFungsionalDosen', [
        //     'filter' => "id_dosen = '4009403f-01ef-413c-9480-bf04b12f2e7d'"
        // ]);
        // $mba_forlapdosen = $this->feederapi->post('GetRiwayatPangkatDosen', [
        //     'filter' => "id_dosen = 'dd1c9811-2f67-41b5-9cf5-b8db5f09d35e'"
        // ]);
        // $mba_forlapdosen = $this->feederapi->post('GetRiwayatPendidikanDosen', [
        //     'filter' => "id_dosen = 'dd1c9811-2f67-41b5-9cf5-b8db5f09d35e'"
        // ]);
        // $mba_forlapdosen = $this->feederapi->post('GetRiwayatSertifikasiDosen', [
        //     'filter' => "id_dosen = 'dd1c9811-2f67-41b5-9cf5-b8db5f09d35e'"
        // ]);
        $mba_forlapdosen = $this->feederapi->post('GetRiwayatPenelitianDosen', [
            'filter' => "id_dosen = '1e6547f8-d6ba-4356-b389-8663d78cf11a'"
        ]);
        print('<pre>');var_dump($mba_forlapdosen);exit;
        // $mba_forlapdosen = $this->feederapi->post('GetListDosen');
        
        // if (($mba_forlapdosen->error_code == 0) AND (count($mba_forlapdosen->data) > 0)) {
        //     $s_file_path = APPPATH."uploads/temp/";
        //     if(!file_exists($s_file_path)){
        //         mkdir($s_file_path, 0777, TRUE);
        //     }
        //     $o_spreadsheet = new Spreadsheet();
        //     $o_spreadsheet->getProperties()->setCreator("IULI ISTS");
        //     $o_sheet = $o_spreadsheet->getActiveSheet();
            
        //     $a_forlapdosen = $mba_forlapdosen->data;
        //     $o_row = 1;
        //     $o_sheet->setCellValue("A$o_row", "Nama Dosen");
        //     $o_sheet->setCellValue("B$o_row", "NIDN");
        //     $o_sheet->setCellValue("C$o_row", "Jenis Kelamin");
        //     $o_sheet->setCellValue("D$o_row", "Agama");
        //     $o_sheet->setCellValue("E$o_row", "Status Feeder");

        //     $o_row++;
        //     foreach ($a_forlapdosen as $o_dosen) {
        //         $o_sheet->setCellValue("A$o_row", $o_dosen->nama_dosen);
        //         $o_sheet->setCellValue("B$o_row", $o_dosen->nidn);
        //         $o_sheet->setCellValue("C$o_row", ($o_dosen->jenis_kelamin == 'L') ? 'Laki - Laki' : 'Perempuan');
        //         $o_sheet->setCellValue("D$o_row", $o_dosen->nama_agama);
        //         $o_sheet->setCellValue("E$o_row", $o_dosen->nama_status_aktif);
        //         $o_row++;
        //     }
        // }
    }

    public function fill_pin_number()
    {
        $s_template_path = APPPATH.'uploads/temp/student_graduation_revision.xlsx';
        $s_file_name = 'student_graduation_revision.xlsx';
        $s_file_path = APPPATH."uploads/temp/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $this->load->library('FeederAPI', ['mode' => 'production']);
		
        $i_row = 2;
        while ($o_sheet->getCell('B'.$i_row)->getValue() !== NULL) {
            // if (is_null($o_sheet->getCell('A'.$i_row)->getValue())) {
                $s_student_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('B'.$i_row)->getValue()));
                $mba_student_data = $this->Stm->get_student_filtered([
                    'dpd.personal_data_name' => $s_student_name,
                    'ds.student_status' => 'graduated'
                ]);

                if ($mba_student_data) {
                    $s_student_id = $mba_student_data[0]->student_id;
                    $a_get_detail_data = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', [
                        'filter' => "id_registrasi_mahasiswa = '$s_student_id'"
                    ]);

                    if (($a_get_detail_data->error_code == 0) AND (count($a_get_detail_data->data) > 0)) {
                        $s_nim = $a_get_detail_data->data[0]->nim;
                        $o_sheet->setCellValue('C'.$i_row, '="'.$s_nim.'"');
                    }
                }
            // }
            $i_row++;
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_file_name);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_file_path.$s_file_name );
        exit;
    }

    public function fill_student_data()
    {
        $s_template_path = APPPATH.'uploads/templates/academic/student_graduation_data.xlsx';
        $s_file_name = 'student_graduation_2021_1.xlsx';
        $s_file_path = APPPATH."uploads/temp/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $i_row = 2;
        while ($o_sheet->getCell('B'.$i_row)->getValue() !== NULL) {
            $s_student_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('A'.$i_row)->getValue()));
            $s_student_number = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('B'.$i_row)->getValue()));

            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_name' => $s_student_name,
                'ds.student_number' => $s_student_number
            ]);

            if (!$mba_student_data) {
                print('data row number '.$i_row.' not found!');exit;
            }

            $o_student = $mba_student_data[0];
            $student_parent = false;
            $a_parent_name = [];
            $a_parent_email = [];
            $a_parent_contact = [];
            $a_student_contact = [];
            
            if (!is_null($o_student->personal_data_phone)) {
                array_push($a_student_contact, $o_student->personal_data_phone);
            }

            if (!is_null($o_student->personal_data_cellular)) {
                array_push($a_student_contact, $o_student->personal_data_cellular);
            }

            $mbo_student_family = $this->Fmm->get_family_by_personal_data_id($o_student->personal_data_id);
            if ($mbo_student_family) {
                $mba_parent_data = $this->Fmm->get_family_lists_filtered(array(
                    'fmm.family_id' => $mbo_student_family->family_id,
                    'fmm.family_member_status != ' => 'child'
                ));
    
                if ($mba_parent_data) {
                    foreach ($mba_parent_data as $o_parents) {
                        if (!in_array($o_parents->personal_data_email, $a_parent_email)) {
                            array_push($a_parent_email, $o_parents->personal_data_email);
                            array_push($a_parent_name, $o_parents->personal_data_name);

                            if (!is_null($o_parents->personal_data_phone)) {
                                array_push($a_parent_contact, $o_parents->personal_data_phone);
                            }

                            if (!is_null($o_parents->personal_data_cellular)) {
                                array_push($a_parent_contact, $o_parents->personal_data_cellular);
                            }
                        }
                    }
                }
            }

            $o_sheet->setCellValue('C'.$i_row, $o_student->study_program_abbreviation);
            $o_sheet->setCellValue('D'.$i_row, $o_student->student_email);
            $o_sheet->setCellValue('E'.$i_row, $o_student->personal_data_email);
            $o_sheet->setCellValue('F'.$i_row, '="'.implode(' / ', $a_student_contact).'"');
            $o_sheet->setCellValue('G'.$i_row, '="'.implode(' / ', $a_parent_name).'"');
            $o_sheet->setCellValue('H'.$i_row, '="'.implode(' / ', $a_parent_contact).'"');
            $o_sheet->setCellValue('I'.$i_row, '="'.implode(' / ', $a_parent_email).'"');

            $i_row++;
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_file_name);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_file_path.$s_file_name );
        exit;
    }

    public function check_invoice_full_payment()
    {
        $mba_unpaid_invoice = $this->Im->get_unpaid_invoice_full(false, ['02']);
        if ($mba_unpaid_invoice) {
            // print('<pre>');var_dump($mba_unpaid_invoice);exit;
            foreach ($mba_unpaid_invoice as $o_invoice) {
                $mba_fullpayment_invoice = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                $mba_installment_invoice = $this->Im->get_invoice_installment($o_invoice->invoice_id);

                $mba_has_paid = $this->Im->get_invoice_data([
                    'di.invoice_id' => $o_invoice->invoice_id,
                    'dsid.sub_invoice_details_amount_paid > ' => 0
                ]);

                if ($mba_has_paid) {
                    $d_unpaid_payment = 0;
                    if ($mba_installment_invoice) {
                        foreach ($mba_installment_invoice as $o_installment) {
                            if (($o_installment->sub_invoice_details_amount_total > 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                $d_unpaid_payment += $o_installment->sub_invoice_details_amount_total;
                            }
                        }
                    }

                    if (!$mba_fullpayment_invoice) {
                        print('no detail found!'.$o_invoice->invoice_id);print('<br>');
                    }
                    else if ($mba_fullpayment_invoice->sub_invoice_details_amount_total != $d_unpaid_payment) {
                        print($o_invoice->invoice_id.' fee semester '.$o_invoice->semester_id);print('<br>');
                    }
                }
            }
        }
    }

    public function update_graduate_year()
    {
        $mba_student_graduate = $this->Stm->get_student_filtered([
            'ds.student_status' => 'graduated',
            'ds.graduated_year_id ' => '2020'
        ], false, 'dpd.personal_data_name');

        if ($mba_student_graduate) {
            $a_student_semester_update = array(
                'student_semester_status' => 'graduated'
            );

            $i = 1;
            foreach ($mba_student_graduate as $o_student) {
                $mba_student_semester = $this->General->get_where('dt_student_semester', [
                    'student_id' => $o_student->student_id,
                    'student_semester_status' => 'graduated'
                ]);

                $s_student_graduate_year = ($mba_student_semester) ? $mba_student_semester[0]->academic_year_id : '';
                print($i++.'. ');
                print($o_student->personal_data_name.'/'.$s_student_graduate_year);
                print('<br>');
                // $a_student_semester_conditional = array(
                //     'student_id' => $o_student->student_id,
                //     'academic_year_id' => date('Y', strtotime($o_student->student_date_graduated)),
                //     'semester_type_id' => 1
                // );

                // $submit_data = $this->Smm->save_student_semester($a_student_semester_update, $a_student_semester_conditional);
                // if ($submit_data) {
                //     print($i++.'. ');
                //     print($o_student->personal_data_name.' updated');
                //     print('<br>');
                // }
                // else {
                //     print('<pre>');var_dump($a_student_semester_conditional);exit;
                // }
            }
        }
    }

    public function fill_data_f()
    {
        $s_template_path = APPPATH.'uploads/temp/paydata-20190101_20221208_5446_150331.xlsx';
        $s_file_name = 'paydata-20190101_20221208_5446_150331_update_student.xlsx';
        $s_file_path = APPPATH."uploads/temp/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $a_datanotfound = [];

        $i_row = 2;
        while ($o_sheet->getCell('E'.$i_row)->getValue() !== NULL) {
            $s_va_number = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('D'.$i_row)->getValue()));
            $s_student_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('E'.$i_row)->getValue()));
            $s_va_payment = substr($s_va_number,0, 6);

            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_name' => $s_student_name
            ]);

            if (($mba_student_data) AND (count($mba_student_data) > 1)) {
                $mba_student_data = $this->Stm->get_student_filtered([
                    'dpd.personal_data_name' => $s_student_name,
                    'ds.student_status != ' => 'resign'
                ]);
            }
            // print($s_va_payment);exit;
            // $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_name' => $s_student_name]);
            // if (!$mba_personal_data) {
            //     if ($s_va_payment != '831001') {
            //         if (!in_array($s_student_name, $a_datanotfound)) {
            //             array_push($a_datanotfound, $s_student_name);
            //         }
            //     }
            //     // print($i_row.'.'.$s_student_name);
            //     // exit;
            // }
            if ($mba_student_data) {
                $o_student_data = $mba_student_data[0];
                $o_sheet->setCellValue('L'.$i_row, '="'.$o_student_data->student_number.'"');
                $o_sheet->setCellValue('M'.$i_row, '="'.$o_student_data->study_program_name.'"');
                $o_sheet->setCellValue('N'.$i_row, '="'.$o_student_data->faculty_name.'"');
            }

            $i_row++;
        }

        // if (count($a_datanotfound) > 0) {
        //     print('<pre>');var_dump($a_datanotfound);exit;
        // }
        // else {
            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_file_name);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
            readfile( $s_file_path.$s_file_name );
            exit;
        // }
    }
    
    public function create_invoice_srh()
    {
        // print('closed');exit;
        $s_personal_data_id = 'ce07e972-7a21-48d3-b985-8cea49fd7c6e';
        $d_amount = 500000;

        $this->load->model("partner/Partner_student_model", 'Psm');
        $mba_student_partner_data = $this->Psm->get_partner_student_data([
            'sn.personal_data_id' => $s_personal_data_id
        ]);

        if ($mba_student_partner_data) {
            $o_student_partner = $mba_student_partner_data[0];
            $s_sequence = substr($o_student_partner->student_partner_number, (strlen($o_student_partner->student_partner_number) - 2), 2);
            $s_period = (is_null($o_student_partner->partner_period_id)) ? '20231' : ($o_student_partner->period_year.$o_student_partner->partner_period);
            // $s_invoice_number = 
            $s_date = date('ymd', time());
            $s_last_invoice_counter = $this->Im->get_latest_invoice_number('02', $s_date);
            $s_invoice_number = 'INV-05'.$s_date.'02'.str_pad($s_last_invoice_counter, 3, "0", STR_PAD_LEFT);

            $a_invoice_data = array(
                'personal_data_id' => $s_personal_data_id,
                'invoice_number' => $s_invoice_number,
                'invoice_description' => 'Semester fee MBA 4.0',
                'invoice_allow_fine' => 'no'
            );
            $s_invoice_id = $this->Im->create_invoice($a_invoice_data);
    
            $a_invoice_details_data = array(
                'invoice_id' => $s_invoice_id,
                'fee_id' => '45fd87bc-ffce-47c8-8506-5a8f9b4554c3',
                'invoice_details_amount' => $d_amount,
                'invoice_details_amount_number_type' => 'number',
                'invoice_details_amount_sign_type' => 'positive'
            );
            $this->Im->create_invoice_details($a_invoice_details_data);
    
            $a_sub_invoice_data = array(
                'sub_invoice_amount' => $d_amount,
                'sub_invoice_amount_total' => $d_amount,
                'invoice_id' => $s_invoice_id,
                'sub_invoice_type' => 'installment'
            );
            $s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);
            $s_deadline_date = date('Y-m-d 23:59:59', strtotime('2023-08-31'));
            // $s_payment_deadline_date = $s_deadline_date;
            
            for ($installment=1; $installment <= 1; $installment++) { 
                $mbs_va_number_partner = $this->Bnim->get_va_partner(
                    '2',
                    '1',
                    $installment,
                    'accepted',
                    $s_period,
                    $o_student_partner->study_program_code,
                    $o_student_partner->partner_program_id,
                    $s_sequence
                );
                // $d_installent_amount = $d_amount / 4;
                $d_installent_amount = $d_amount / 1;
                $s_payment_deadline_date = ($installment == 1) ? $s_deadline_date : date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date." +1 month"));

                if (($mbs_va_number_partner) AND ($mbs_va_number_partner['code'] == 0)) {
                    $s_va_number_partner = $mbs_va_number_partner['va_number'];
                    $s_description = 'Installment '.$installment.' Semester fee MBA 4.0';

                    $a_sub_invoice_details_data = [
                        'sub_invoice_id' => $s_sub_invoice_id,
                        'sub_invoice_details_amount' => $d_installent_amount,
                        'sub_invoice_details_amount_total' => $d_installent_amount,
                        'sub_invoice_details_va_number' => $s_va_number_partner,
                        'sub_invoice_details_deadline' => $s_payment_deadline_date,
                        'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
                        'sub_invoice_details_description' => $s_description
                    ];
                    $s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);
                    print($s_va_number_partner.'<br>');
                }
                else {
                    print('va '.$i_installment.' ga kebuat!<br>');
                }
            }
        }
    }

    public function force_update_deadline()
    {
        $mba_invoice_unpaid = $this->Im->get_unpaid_invoice([
            'di.academic_year_id' => 2021,
            'di.semester_type_id' => 1,
            'di.invoice_allow_fine' => 'yes',
            'di.invoice_amount_fined > ' => 0
        ]);

        if ($mba_invoice_unpaid) {
            // print('<pre>');
            // var_dump($mba_invoice_unpaid);exit;
            foreach ($mba_invoice_unpaid as $o_invoice) {
                $mba_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                $mbo_full = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                if (($mbo_full) AND (!in_array($mbo_full->sub_invoice_details_status, ['paid', 'cancelled'])) AND (!is_null($mbo_full->trx_id))) {
                    $o_bni_data = $this->Bnim->get_data_by_trx_id($mbo_full->trx_id);
                    $a_update_billing = array(
                        'trx_id' => $mbo_full->trx_id,
                        'trx_amount' => $mbo_full->sub_invoice_details_amount_total,
                        'customer_name' => $o_bni_data->customer_name,
                        'datetime_expired' => date('Y-m-d 23:59:59', strtotime($mbo_full->sub_invoice_details_deadline)),
                        'description' => $mbo_full->sub_invoice_details_description,
                        'customer_email' => ''
                    );
                    
                    $a_update = $this->Bnim->update_billing($a_update_billing);
                    print(json_encode($a_update));
                    print('<br>');
                }

                if ($mba_installment) {
                    foreach ($mba_installment as $o_installment) {
                        if ((!is_null($o_installment->trx_id)) AND (!in_array($o_installment->sub_invoice_details_status, ['paid', 'cancelled']))) {
                            $o_bni_data = $this->Bnim->get_data_by_trx_id($o_installment->trx_id);
                            $a_update_billing = array(
                                'trx_id' => $o_installment->trx_id,
                                'trx_amount' => $o_installment->sub_invoice_details_amount_total,
                                'customer_name' => $o_bni_data->customer_name,
                                'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_deadline)),
                                'description' => $o_installment->sub_invoice_details_description,
                                'customer_email' => ''
                            );
                            
                            $a_update = $this->Bnim->update_billing($a_update_billing);
                            print(json_encode($a_update));
                            print('<br>');
                        }
                    }
                }
            }
        }
    }
    public function test_bni_dev()
    {
        $s_va_number = $this->Bnim->get_va_number(
            '01',
            0,
            0,
            'candidate',
            null,
            2021,
            1
        );

        if ($this->session->userdata('environment') != 'production') {
            print('<pre>');
            var_dump($s_va_number);exit;
            // $a_billing_data = array(
            //     'trx_amount' => 200000,
            //     'billing_type' => 'i',
            //     'customer_name' => 'budi siswanto',
            //     'virtual_account' => $s_va_number,
            //     'description' => 'testing',
            //     'datetime_expired' => date('Y-m-d H:i:s', time()),
            // );
            
            // $billing_created = $this->Bnim->create_billing($a_billing_data);
            // print('<pre>');
            // var_dump($billing_created);exit;
        }
        else {
            print('production!!!');
        }
    }

    public function set_fixed_billing()
    {
        print('function closed!!!');
        $a_trx_id = [];
        // $a_trx_id = ['993718313','1804820363','1030855409','816468165','1375678388','1822291720','2062698407','1863332562','780955052','1404617282','742540848','264703786','1189373562'];
        foreach ($a_trx_id as $s_trx_id) {
            $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', ['trx_id' => $s_trx_id]);
            if (!$mba_sub_invoice_details) {
                print($s_trx_id);exit;
            }
            else if (count($mba_sub_invoice_details) > 1) {
                print('<pre>');
                var_dump($mba_sub_invoice_details);
                exit;
            }
            $mba_invoice_details = $this->Im->get_invoice_detail_by_trx_id($s_trx_id);

            $o_sub_invoice_details = $mba_sub_invoice_details[0];
            $a_update_billing = array(
				'trx_id' => $o_sub_invoice_details->trx_id,
				'trx_amount' => (floatval($o_sub_invoice_details->sub_invoice_details_amount) + floatval($o_sub_invoice_details->sub_invoice_details_amount_fined)),
				'customer_name' => $mba_invoice_details->customer_name,
				'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +2 day")),
				'description' => $o_sub_invoice_details->sub_invoice_details_description,
			);
			
			$update = $this->Bnim->update_billing($a_update_billing);
            print('<pre>');
            var_dump($update);
        }
    }

    public function database_structure()
    {
        $s_db = $this->db->database;
        $table_skipped = ['a_score_removed', 'a_table_testing'];
        $list_table_key = [];
        $table_number = 1;

        $tables = $this->db->list_tables();
        foreach ($tables as $table_list) {
            if (!in_array($table_list, $table_skipped)) {
                $fields_table_search = $this->db->field_data($table_list);
                foreach ($fields_table_search as $o_field_search) {
                    if ($o_field_search->primary_key == '1') {
                        $list_table_key[$o_field_search->name] = $table_list;
                        break;
                    }
                }
            }
        }

        foreach ($tables as $s_table) {
            if (!in_array($s_table, $table_skipped)) {
                $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$s_db' AND TABLE_NAME ='$s_table';";
                $desc = $this->db->query($sql)->result();
                if (count($desc) > 0) {
                    foreach ($desc as $o_desc) {
                        $s_table_link = '';
                        if ($o_desc->COLUMN_KEY == 'MUL') {
                            if (array_key_exists($o_desc->COLUMN_NAME, $list_table_key)) {
                                $s_table_link = $list_table_key[$o_desc->COLUMN_NAME];
                            }
                        }
                        $o_desc->LINK_COLUMN = $s_table_link;
                    }

                    print('<p><br></p>');
                    print('<strong>Table '.$table_number++.': Structure of table '.$s_table.'</strong><br>');
                    print('<table border="1" width="100%">');
                    print('<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th><th>Link Table</th><th>Comment</th></tr>');
                    foreach ($desc as $o_data) {
                        print('<tr>');
                        print('<td>'.$o_data->COLUMN_NAME.'</td>');
                        print('<td>'.$o_data->COLUMN_TYPE.'</td>');
                        print('<td>'.$o_data->IS_NULLABLE.'</td>');
                        print('<td>'.$o_data->COLUMN_KEY.'</td>');
                        print('<td>'.$o_data->COLUMN_DEFAULT.'</td>');
                        print('<td>'.$o_data->EXTRA.'</td>');
                        print('<td>'.$o_data->LINK_COLUMN.'</td>');
                        print('<td>'.$o_data->COLUMN_COMMENT.'</td>');
                        print('</tr>');
                    }
                    print('</table>');
                }
            }
        }
    }

    public function show_serve()
    {
        print('<pre>');
        var_dump($_SERVER);exit;
    }

    public function show_session()
    {
        print('<pre>');
        var_dump($this->session->userdata());exit;
    }

    // public function sync_attendance()
    // {
    //     $this->load->model('portal/Portal_model', 'Prm');
    //     $this->load->model('hris/Hris_model', 'Hrm');

    //     $portal_att_data = $this->Prm->retrieve_data('Logs');
    //     $a_id_log_not_found = [];
    //     $i_count = 0;
    //     if ($portal_att_data) {
    //         foreach ($portal_att_data as $o_data) {
    //             $mbo_hid_data = $this->Hrm->get_hid_data($o_data->HID);
    //             $a_data_sync = [
    //                 'log_id' => $this->uuid->v4(),
    //                 'hid_id' => NULL,
    //                 'hid_key' => $o_data->HID,
    //                 'log_date' => $o_data->Log_Date,
    //                 'log_checkin' => $o_data->Checkin,
    //                 'log_checkout' => $o_data->Checkout,
    //                 'log_late_in' => $o_data->Late_In,
    //                 'log_early_out' => $o_data->Early_Out,
    //                 'log_working_hour' => $o_data->Effective_Working_Hour,
    //                 'date_added' => date('Y-m-d H:i:s'),
    //                 'portal_id' => $o_data->Id_Log
    //             ];
    //             if ($mbo_hid_data) {
    //                 $a_data_sync['hid_id'] = $mbo_hid_data->hid_id;
    //             }

    //             $mba_attendace_submitted = $this->Hrm->get_log_absence([
    //                 'portal_id' => $o_data->Id_Log
    //             ]);
    //             if ($mba_attendace_submitted) {
    //                 unset($a_data_sync['log_id']);
    //                 $this->Hrm->submit_attendace($a_data_sync, [
    //                     'log_id' => $mba_attendace_submitted[0]->log_id
    //                 ]);
    //             }
    //             else {
    //                 $this->Hrm->submit_attendace($a_data_sync);
    //             }
    //             $i_count++;
    //         }
    //     }
    //     print('<h1>'.$i_count.' data diproses</h1>');
    //     // 
    // }

    public function get_reminder($a_params)
    {
        // $i_start_row = 0;
        // $rows_per_page = 100;
        // $a_params = array(
        //     'start_date' => '2020-08-01',
        //     'end_date' => '2021-05-11',
        //     'program_id' => '',
        //     'faculty_id' => '',
        //     'study_program_id' => '',
        //     'page' => 1
        // );

        // $i_counter = 0;
        // $a_data = [];

        // if (($a_params['start_date'] != "") AND ($a_params['end_date'] != "")) {
        //     $diff  = (strtotime($s_end_date) - strtotime($a_params['start_date']));
        //     $in_days = ($diff / (60 * 60 * 24)) + 1;
        //     $s_date = $a_params['start_date'];

        //     for ($i=0; $i < $in_days; $i++) {
        //         $date_file = date('Y-m-d', strtotime($s_date));
        //         $s_file_name = "reminder_date_".str_replace('-', '_', $date_file);
        //         $s_json_file = APPPATH."uploads/reminder/".date('Y', strtotime($s_date))."/".date('m', strtotime($s_date))."/".$s_file_name.".json";
        //         if (file_exists($s_json_file)) {
        //             $s_file_json = file_get_contents($s_json_file);
        //             $a_file_json = json_decode($s_file_json);
        //             if (count($a_file_json) > 0) {
        //                 $i_counter += count($a_file_json);
        //             }
        //         }
        //     }

        //     for ($i=0; $i < $in_days; $i++) {
        //         if ($i == $rows_per_page) {
        //             break;
        //         }

        //         $date_file = date('Y-m-d', strtotime($s_date));
        //         $s_file_name = "reminder_date_".str_replace('-', '_', $date_file);
        //         $s_json_file = APPPATH."uploads/reminder/".date('Y', strtotime($s_date))."/".date('m', strtotime($s_date))."/".$s_file_name.".json";
        //         // print($s_json_file.'<br>');
        //         if (file_exists($s_json_file)) {
        //             $s_file_json = file_get_contents($s_json_file);
        //             if ($s_file_json) {
        //                 $a_file_json = json_decode($s_file_json);
        //                 // print('<pre>');var_dump($a_file_json);exit;
                        
        //                 if (count($a_file_json) > 0) {
        //                     foreach ($a_file_json as $a_json) {
        //                         $a_json->date = date('d M Y H:i:s', strtotime($a_json->date));
        //                         $a_json->email = implode('; ', $a_json->to);
        //                         $s_student_email = explode(' ', $a_json->email)[0];

        //                         $mbo_student_data = $this->Stm->get_student_list_data([
        //                             'ds.student_email' => trim($s_student_email)
        //                         ]);
        //                         $a_json->personal_data_name = ($mbo_student_data) ? $mbo_student_data[0]->personal_data_name : "";
        //                         $a_json->academic_year_id = ($mbo_student_data) ? $mbo_student_data[0]->academic_year_id : "";
        //                         $a_json->study_program_abbreviation = ($mbo_student_data) ? $mbo_student_data[0]->study_program_abbreviation : "";

        //                         if (($a_json->cc !== null) AND ($a_json->cc != "")) {
        //                             // print('<pre>');var_dump($a_json->cc);
        //                             $a_json->cc = implode('; ', $a_json->cc);
        //                         }
                                
        //                         // if ($this->input->post('student_name')) {
        //                         //     # code...
        //                         // }

        //                         // if ($this->input->post('student_email') !== null) {
        //                         //     $found_email = false;
        //                         //     if (count($a_json['email']) > 0) {
        //                         //         foreach ($a_json['email'] as $s_received) {
        //                         //             if ($s_received == $this->input->post('student_email')) {
        //                         //                 $found_email = true;
        //                         //             }
        //                         //         }
        //                         //     }

        //                         //     if ($found_email) {
        //                         //         array_push($a_data, $a_json);
        //                         //     }
        //                         // }

        //                         // if ($this->input->post('parent_email') !== null) {
        //                         //     $found_email = false;
        //                         //     if (count($a_json['cc']) > 0) {
        //                         //         foreach ($a_json['cc'] as $s_received) {
        //                         //             if ($s_received == $this->input->post('parent_email')) {
        //                         //                 $found_email = true;
        //                         //             }
        //                         //         }
        //                         //     }

        //                         //     if ($found_email) {
        //                         //         array_push($a_data, $a_json);
        //                         //     }
        //                         // }

        //                         array_push($a_data, $a_json);
        //                     }
        //                 }
        //             }
        //         }
        //         // else {
        //         //     print($s_json_file.'<br>');
        //         // }
        //         $s_date = date('Y-m-d', strtotime($date_file." +1 days"));
        //     }

        //     $a_data = array_values($a_data);
        // }
        // $s_date = $s_start_date;
        
        // if ($i_counter > 0) {
        //     $a_return = [
        //         'draw' => $a_params['page'],
        //         'recordTotal' => $i_counter,
        //         'recordsFiltered' => count($a_data),
        //         'data' => $a_data
        //     ];
        // }

        // print($i_counter);exit;
    }

    public function list_reminder()
    {
        if ($this->input->is_ajax_request()) {
            $s_start_date = $this->input->post('reminder_start_date');
            $s_end_date = $this->input->post('reminder_end_date');

            $a_data = [];

            if (($s_start_date != "") AND ($s_end_date != "")) {
                $diff  = (strtotime($s_end_date) - strtotime($s_start_date));
                $in_days = ($diff / (60 * 60 * 24)) + 1;

                $s_date = $s_start_date;

                for ($i=0; $i < $in_days; $i++) {
                    $date_file = date('Y-m-d', strtotime($s_date));
                    $s_file_name = "reminder_date_".str_replace('-', '_', $date_file);
                    $s_json_file = APPPATH."uploads/reminder/".date('Y', strtotime($s_date))."/".date('m', strtotime($s_date))."/".$s_file_name.".json";
                    // print($s_json_file.'<br>');
                    if (file_exists($s_json_file)) {
                        $s_file_json = file_get_contents($s_json_file);
                        if ($s_file_json) {
                            $a_file_json = json_decode($s_file_json);
                            // print('<pre>');var_dump($a_file_json);exit;
                            
                            if (count($a_file_json) > 0) {
                                foreach ($a_file_json as $a_json) {
                                    $a_json->date = date('d M Y H:i:s', strtotime($a_json->date));
                                    $a_json->email = implode('; ', $a_json->to);
                                    $s_student_email = explode(' ', $a_json->email)[0];

                                    $mbo_student_data = $this->Stm->get_student_list_data([
                                        'ds.student_email' => trim($s_student_email)
                                    ]);
                                    $a_json->personal_data_name = ($mbo_student_data) ? $mbo_student_data[0]->personal_data_name : "";
                                    $a_json->academic_year_id = ($mbo_student_data) ? $mbo_student_data[0]->academic_year_id : "";
                                    $a_json->study_program_abbreviation = ($mbo_student_data) ? $mbo_student_data[0]->study_program_abbreviation : "";

                                    if (($a_json->cc !== null) AND ($a_json->cc != "")) {
                                        // print('<pre>');var_dump($a_json->cc);
                                        $a_json->cc = implode('; ', $a_json->cc);
                                    }
                                    
                                    // if ($this->input->post('student_name')) {
                                    //     # code...
                                    // }

                                    // if ($this->input->post('student_email') !== null) {
                                    //     $found_email = false;
                                    //     if (count($a_json['email']) > 0) {
                                    //         foreach ($a_json['email'] as $s_received) {
                                    //             if ($s_received == $this->input->post('student_email')) {
                                    //                 $found_email = true;
                                    //             }
                                    //         }
                                    //     }

                                    //     if ($found_email) {
                                    //         array_push($a_data, $a_json);
                                    //     }
                                    // }

                                    // if ($this->input->post('parent_email') !== null) {
                                    //     $found_email = false;
                                    //     if (count($a_json['cc']) > 0) {
                                    //         foreach ($a_json['cc'] as $s_received) {
                                    //             if ($s_received == $this->input->post('parent_email')) {
                                    //                 $found_email = true;
                                    //             }
                                    //         }
                                    //     }

                                    //     if ($found_email) {
                                    //         array_push($a_data, $a_json);
                                    //     }
                                    // }

                                    array_push($a_data, $a_json);
                                }
                            }
                        }
                    }
                    // else {
                    //     print($s_json_file.'<br>');
                    // }
                    $s_date = date('Y-m-d', strtotime($date_file." +1 days"));
                }

                $a_data = array_values($a_data);
            }
            print json_encode(['code' => 0, 'data' => $a_data]);
        }else{
            $this->a_page_data['body'] = $this->load->view('devs/reminder/list', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function info()
    {
        print(phpinfo());
    }

    public function check_reminder()
    {
        $this->load->model('Activities_model', 'Am');
        // $invoice_list = $this->General->get_where('dt_invoice', ['date(date_added) < ' => '2020-12-01']);
        $invoice_list = $this->Im->get_invoice_list_detail([
            'date(di.date_added) < ' => '2020-12-01',
            'fee.payment_type_code' => '02'
        ]);

        if ($invoice_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Report_reminder_8-8-20_31-12-20.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Invoice Reminder Report");

            $i_row = 1;
            $o_sheet->setCellValue('A'.$i_row, 'Student Name');
            $o_sheet->setCellValue('B'.$i_row, 'Invoice Number');
            $o_sheet->setCellValue('B'.$i_row, 'Invoice Status');
            $o_sheet->setCellValue('C'.$i_row, 'Invoice Description');
            $i_row++;

            foreach ($invoice_list as $o_invoice) {
                $s_pos = strpos($o_invoice->invoice_description, "Enrollment");
                $c_reminder = 'E';

                if (!$s_pos) {
                    $a_student_status_allowed = ['active', 'inactive', 'onleave', 'dropout', 'graduated'];
                    $mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id]);
                    if (($mba_student_data) AND (in_array($mba_student_data[0]->student_status, $a_student_status_allowed))) {
                        $a_reminder_date = [];
                        $o_student_data = $mba_student_data[0];

                        if ($o_invoice->invoice_status == 'paid') {
                            $fee_amount = $o_invoice->fee_amount;
                            if ($o_invoice->fee_amount != $o_invoice->invoice_amount_paid) {
                                $o_sheet->getStyle('D'.$i_row)->getFill()
                                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('40BA30');
                            }
                        }

                        $o_sheet->setCellValue('A'.$i_row, $o_student_data->personal_data_name);
                        $o_sheet->setCellValue('B'.$i_row, $o_invoice->invoice_number);
                        $o_sheet->setCellValue('C'.$i_row, $o_invoice->invoice_description);
                        $o_sheet->setCellValue('D'.$i_row, $o_invoice->invoice_status);

                        $reminder_8 = $this->Am->get_reminder_activities('reminder_log_202008', [
                            'invoice_number' => $o_invoice->invoice_number
                        ]);

                        if ($reminder_8) {
                            foreach ($reminder_8 as $o_reminder) {
                                array_push($a_reminder_date, $o_reminder->reminder_date);
                            }
                        }

                        $reminder_9 = $this->Am->get_reminder_activities('reminder_log_202009', [
                            'invoice_number' => $o_invoice->invoice_number
                        ]);
                        if ($reminder_9) {
                            foreach ($reminder_9 as $o_reminder) {
                                array_push($a_reminder_date, $o_reminder->reminder_date);
                            }
                        }

                        $reminder_10 = $this->Am->get_reminder_activities('reminder_log_202010', [
                            'invoice_number' => $o_invoice->invoice_number
                        ]);
                        if ($reminder_10) {
                            foreach ($reminder_10 as $o_reminder) {
                                array_push($a_reminder_date, $o_reminder->reminder_date);
                            }
                        }

                        $reminder_11 = $this->Am->get_reminder_activities('reminder_log_202011', [
                            'invoice_number' => $o_invoice->invoice_number
                        ]);
                        if ($reminder_11) {
                            foreach ($reminder_11 as $o_reminder) {
                                array_push($a_reminder_date, $o_reminder->reminder_date);
                            }
                        }

                        $reminder_12 = $this->Am->get_reminder_activities('reminder_log_202012', [
                            'invoice_number' => $o_invoice->invoice_number
                        ]);
                        if ($reminder_12) {
                            foreach ($reminder_12 as $o_reminder) {
                                array_push($a_reminder_date, $o_reminder->reminder_date);
                            }
                        }

                        $o_invoice->reminder = $a_reminder_date;

                        if (count($a_reminder_date) > 0) {
                            foreach ($a_reminder_date as $s_date) {
                                $o_sheet->setCellValue($c_reminder.$i_row, date('Y-m-d', strtotime($s_date)));
                                $c_reminder++;
                            }
                        }
                        // $o_invoice->personal_data_name = $o_student_data->personal_data_name;
                        // print('<pre>');var_dump($o_invoice);exit;

                        $i_row++;
                    }
                }
                // exit;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_file_name);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
            readfile( $s_file_path.$s_file_name );
            exit;
        }
    }

    public function mail_reminder($i_start = 3200)
    {
        $this->load->model('Activities_model', 'Am');
        $this->load->library('Imap_new', [
			'mailbox' => '',
			'username' => 'employee@company.ac.id',
			'password' => ''
		]);

        // $i_end = $i_start + 5000;
        $i_end = 36501;

        // $s_folders = $this->imap_new->selectFolder('INBOX.2020');
        $s_folders = $this->imap_new->selectFolder('INBOX');
        $i_counter_message = $this->imap_new->countMessages();
        // print($i_counter_message);exit;
        $a_file_json = [];
        for ($i=$i_start; $i < $i_end; $i++) {
            // $mpdf = new \Mpdf\Mpdf([
            //     'default_font_size' => 9,
            //     'default_font' => 'sans_fonts',
            //     'mode' => 'utf-8',
            //     'format' => 'A4-P'
            // ]);

            $a_inbox_items = $this->imap_new->getMessage($i);
            $s_date_mail = date('Y-m-d H:i:s', strtotime($a_inbox_items['date']));
            // print($s_date_mail);
            // print('<pre>');var_dump($a_inbox_items);exit;

            if ((is_array($a_inbox_items['to']))) {
                $a_mail_to = explode(' ', $a_inbox_items['to'][0]);
                $s_mailto = $a_mail_to[0];
            }
            else {
                $s_mailto = $a_inbox_items['to'];
            }
            $mba_student_data = $this->General->get_where('dt_student', ['student_email' => $s_mailto]);
            $a_mail_cc = [];
            if ((isset($a_inbox_items['cc'])) AND (count($a_inbox_items['cc']) > 0)) {
                foreach ($a_inbox_items['cc'] as $s_mail_cc) {
                    $a_cc = explode(' ', $s_mail_cc);
                    if (!in_array($a_cc[0], $a_mail_cc)) {
                        array_push($a_mail_cc, $a_cc[0]);
                    }
                }
            }

            if ($mba_student_data) {
                if (is_null($mba_student_data[0]->personal_data_id)) {
                    print('failed retrieve student data!');
                    print('<pre>');var_dump($a_inbox_items);
                    exit;
                }

                $s_pos = strpos($a_inbox_items['body'], "Invoice Number:");
                $s_invoice_number = md5(time());
                if ($s_pos) {
                    $s_invoice_number = trim(substr($a_inbox_items['body'], ($s_pos + 15), 20));
                }

                $a_student_email = explode('@', $mba_student_data[0]->student_email);
                $s_student_email = str_replace('.', '_', trim($a_student_email[0]));
                // print($s_student_email);exit;

                $a_invoice_number = explode('<', $s_invoice_number);
                $s_invoice_number = $a_invoice_number[0];
                $s_fileday = date('dmY', strtotime($s_date_mail));
                $s_filename = $s_student_email.'_'.$s_fileday.'_'.$s_invoice_number.'.pdf';

                $s_mail_st = date('Ym', strtotime($s_date_mail));
                $s_mail_st = ($s_mail_st == date('Ym')) ? '' : '_'.$s_mail_st;
                $reminder_file = $this->Am->get_reminder_activities('reminder_log'.$s_mail_st, [
                    'reminder_file' => $s_filename
                ], true);
                // print('<pre>');var_dump($reminder_file);exit;

                if ($reminder_file) {
                    $s_fileday = $s_fileday.'-'.count($reminder_file);
                }
                $s_filename = $s_student_email.'_'.$s_fileday.'_'.$s_invoice_number.'.pdf';

                $mba_exists = $this->Am->get_reminder_activities('reminder_log'.$s_mail_st, [
                    'reminder_uid' => $a_inbox_items['uid']
                ]);

                if ($mba_exists) {
                    $s_filename = $mba_exists[0]->reminder_file;
                }
                
                $a_mail_json = [
                    'reminder_to' => $a_mail_to[0],
                    'reminder_type' => 'invoice_student',
                    'personal_data_id' => ($mba_student_data) ? $mba_student_data[0]->personal_data_id : NULL,
                    'reminder_date' => $s_date_mail,
                    'reminder_uid' => (isset($a_inbox_items['uid'])) ? $a_inbox_items['uid'] : '',
                    'reminder_cc' => json_encode($a_mail_cc),
                    'reminder_file' => $s_filename,
                    'invoice_number' => $s_invoice_number
                ];
                // print('<pre>');var_dump($a_mail_json);exit;

                // $s_dir = APPPATH.'uploads/finance/reminder/'.date('Y', strtotime($s_date_mail)).'/'.date('m', strtotime($s_date_mail)).'/';
                // $s_dir = APPPATH.'uploads/'.$mba_student_data[0]->personal_data_id.'/invoice/reminder/'.date('Y', strtotime($s_date_mail)).'/'.date('m', strtotime($s_date_mail)).'/';
                // echo($s_dir.$s_filename);exit;
                // if(!file_exists($s_dir)){
                //     mkdir($s_dir, 0777, TRUE);
                // }

                // $s_html = $a_inbox_items['body'];
                // $mpdf->WriteHTML($s_html);
                // $mpdf->Output($s_dir.$s_filename, 'F');

                // if(file_exists($s_dir.$s_filename)){
                    $this->Am->submit_reminder($a_mail_json);
                    print($i.': '.$s_filename);
                    print('<br>');
                // }
                // else {
                //     print('ksong:'.$i);exit;
                // }
                
                // array_push($a_file_json, $a_mail_json);
                // print('<pre>');var_dump($s_filename);exit;
            }
        }

        $this->send_notification_telegram('Finish-'.$i);
        $execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        print('<h1>'.$execution_time.' second</h1>');

        // $s_json_data = json_encode($a_file_json);
        // print('<pre>');var_dump($s_json_data);exit;
    }

    public function mailcrawling($s_folder = '2020')
    {
        print('<pre>');
        print('<h2>Closed</h2>');exit;
        $this->load->library('Imap_new', [
			'mailbox' => '',
			'username' => 'employee@company.ac.id',
			'password' => ''
		]);

        $s_folders = $this->imap_new->selectFolder('INBOX.'.$s_folder);
        // $a_folders = $this->imap->getFolders();
        $i_counter_message = $this->imap_new->countMessages();
        // $a_inbox_items = $this->imap_new->getMessage(33);
        // $a_inbox_items = $this->imap->get_attachments(33);
        // $a_message_to = [];
        
        if ($i_counter_message > 0) {
            $s_path = APPPATH."uploads/reminder/";
            
            for ($i=36438; $i < 38860; $i++) {
                $a_inbox_items = $this->imap_new->getMessage($i);
                $s_date_mail = date('Y-m-d', strtotime($a_inbox_items['date']));
                
                // if (date('Y-m', strtotime($a_inbox_items['date'])) == '2020-08') {
                    $s_file_path = $s_path.date('Y', strtotime($s_date_mail)).'/'.date('m', strtotime($s_date_mail)).'/';
                    if(!file_exists($s_file_path)){
                        mkdir($s_file_path, 0777, TRUE);
                    }

                    $s_storefile_json = $s_file_path."reminder_date_".str_replace('-', '_', $s_date_mail).".json";
                    if(!file_exists($s_storefile_json)){
                        file_put_contents($s_storefile_json, json_encode([]));
                    }

                    $a_mail_json = [
                        'to' => (isset($a_inbox_items['to'])) ? $a_inbox_items['to'] : '',
                        'date' => $a_inbox_items['date'],
                        'uid' => (isset($a_inbox_items['uid'])) ? $a_inbox_items['uid'] : '',
                        'cc' => (isset($a_inbox_items['cc'])) ? $a_inbox_items['cc'] : '',
                        'body' => (isset($a_inbox_items['body'])) ? $a_inbox_items['body'] : ''
                    ];
                    
                    $s_file_json = file_get_contents($s_storefile_json);
                    $a_file_json = json_decode($s_file_json);
                    
                    array_push($a_file_json, $a_mail_json);
                    file_put_contents($s_storefile_json, json_encode($a_file_json));
                // }
            }
        }
        // if (count($a_inbox_items) >= 1) {
        //     foreach ($a_inbox_items as $key => $a_item) {
        //         var_dump($a_item);
        //         exit;
        //     }
        // }
		// print('<pre>');
		// print(htmlspecialchars($s_body));exit;
        // print('attachment<br>');
        var_dump($i_counter_message);exit;
        // var_dump(date('Y-m-d H:i:s', strtotime($a_inbox_items['date'])));exit;
    }

    public function set_student_nisn()
    {
        $s_template_path = APPPATH.'uploads/temp/student_2022_nisn.xlsx';
        $o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $i_row = 2;
        $i_count = 1;
        while ($o_sheet->getCell("A".$i_row)->getValue() !== NULL) {
            $s_student_id = $o_sheet->getCell("F$i_row")->getValue();
            $s_student_nisn = $o_sheet->getCell("H$i_row")->getValue();
            $s_student_nisn = str_replace("'", "", $s_student_nisn);

            if (!empty($s_student_nisn)) {
                $this->Stm->update_student_data(['student_nisn' => $s_student_nisn], $s_student_id);
                print($i_count++.' update '.$s_student_id.'/'.$s_student_nisn);
                print('<br>');
            }
            $i_row++;
        }
    }

    public function get_lama_study()
    {
        $s_template_path = APPPATH.'uploads/templates/academic/student_graduation_feb_2022.xlsx';
		
        $s_file_name = 'student_study.xlsx';
        $s_file_path = APPPATH."uploads/temp/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $i_row = 2;
        while ($o_sheet->getCell("B".$i_row)->getValue() !== NULL) {
            $s_student_number = $o_sheet->getCell("B$i_row")->getValue();
            $s_personal_data_name = $o_sheet->getCell("A$i_row")->getValue();

            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number, 'dpd.personal_data_name' => $s_personal_data_name]);
            if (!$mba_student_data) {
                print('student not found at line '.$i_row);exit;
            }

            $s_student_id = $mba_student_data[0]->student_id;
            $mba_student_semester_study = $this->Smm->get_semester_student($s_student_id, ['student_semester_status != ' => 'onleave'], [1,2]);
            $mba_student_semester_onleave = $this->Smm->get_semester_student($s_student_id, ['student_semester_status' => 'onleave'], [1,2]);
            // $mba_student_semester_study = $this->General->get_where('dt_student_semester', ['student_id' => $s_student_id, 'student_semester_status != ' => 'onleave']);
            // $mba_student_semester_onleave = $this->General->get_where('dt_student_semester', ['student_id' => $s_student_id, 'student_semester_status' => 'onleave']);

            $s_count_student_study = ($mba_student_semester_study) ? count($mba_student_semester_study) : 0;
            $s_count_student_onleave = ($mba_student_semester_onleave) ? count($mba_student_semester_onleave) : '';
            $o_sheet->setCellValue('H'.$i_row, $s_count_student_study);
            $o_sheet->setCellValue('I'.$i_row, $s_count_student_onleave);

            $i_row++;
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_file_name);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_file_path.$s_file_name );
        exit;
    }

    public function get_student_submitted_thesis_proposal()
    {
        $a_filter = [
            'ds.student_mark_submitted_thesis_proposal' => 1,
            'ds.student_status' => 'active'
        ];

        $mba_student_data = $this->Stm->get_student_filtered($a_filter);
        return $mba_student_data;
    }

    public function test_find_string()
    {
        $s_path = APPPATH."modules/devs/controllers/*";
        $command = 'find . -type f ! -path "'.$s_path.'" -exec sh -c "APPPATH"';
        $exec = shell_exec($command);
        print('<pre>');
        var_dump($exec);
    }

    public function activate_va_short_semester()
    {
        // print('closed!cek mail to');exit;
        print('<pre>');
        $this->load->model('finance/Bni_model', 'Bm');
        $s_academic_year_id = 2022;
        $s_semester_type_id = 7;

        $mba_score_data = $this->Scm->get_student_by_score([
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id,
            'sc.score_approval' => 'approved'
        ]);

        if ($mba_score_data) {
            $i_count = 0;
            foreach ($mba_score_data as $o_student) {
                $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_student->semester_id])[0];

                $mbo_fee_data = $this->Im->get_fee([
                    'semester_id' => $o_student->semester_id,
                    'payment_type_code' => '04',
                    'program_id' => $o_student->program_id,
                    'study_program_id' => $o_student->study_program_id,
                    'academic_year_id' => $o_student->finance_year_id,
                    'fee_amount_type' => 'main'
                ])[0];
    
                if ($mbo_fee_data) {
                    $mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($o_student->personal_data_id, $mbo_fee_data->fee_id);
                    if (!$mbo_student_invoice_data) {
                        print('err:'.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                        exit;
                    }else {
                        $invoice_full = $this->Im->get_invoice_full_payment($mbo_student_invoice_data->invoice_id);
    
                        if (!$invoice_full) {
                            print('no full payment created:'.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                            print('exit');exit;
                        }else {
                            $invoice_full = $this->Im->get_invoice_full_payment($mbo_student_invoice_data->invoice_id);
    
                            if (!$invoice_full) {
                                print('no full payment created:'.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                                print('exit');exit;
                            }else {
                                if ($invoice_full->sub_invoice_details_amount_paid == 0) {
                                    if(is_null($invoice_full->trx_id)) {
                                        $a_trx_data = array(
                                            'trx_amount' => $invoice_full->sub_invoice_details_amount_total,
                                            'billing_type' => 'c',
                                            'customer_name' => $o_student->personal_data_name,	
                                            'virtual_account' => $invoice_full->sub_invoice_details_va_number,
                                            'description' => $invoice_full->sub_invoice_details_description,
                                            'datetime_expired' => date('Y-m-d 23:59:59', strtotime($invoice_full->sub_invoice_details_deadline)),
                                            'customer_email' => 'bni.employee@company.ac.id'
                                        );
                                        $a_bni_result = $this->Bm->create_billing($a_trx_data);
    
                                        $a_sub_invoice_details_update = array(
                                            'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($invoice_full->sub_invoice_details_deadline))
                                        );
                                        
                                        if($a_bni_result['status'] == '000'){
                                            $a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
                                            $a_return = array('code' => 0, 'message' => 'activate '.$o_student->personal_data_name.' !');
                                        }
                                        else{
                                            $a_return = array('code' => 1, 'message' => 'failed activate data:', 'data: ' => $a_bni_result);
                                            if ((isset($a_bni_result['billing_data']['trx_id'])) AND ($a_bni_result['status'] == '102')) {
                                                // print('<pre>ss');var_dump($a_bni_result);exit;
                                                $a_update_billing = array(
                                                    'trx_id' => $a_bni_result['billing_data']['trx_id'],
                                                    'trx_amount' => 999,
                                                    'customer_name' => 'CANCEL PAYMENT',
                                                    'datetime_expired' => '2023-01-01 23:59:59',
                                                    'description' => 'CANCEL PAYMENT'
                                                );
                                                $this->Bm->update_billing($a_update_billing);
                                            }
                                        }
                                        
                                        $this->Im->update_sub_invoice_details(
                                            $a_sub_invoice_details_update, 
                                            array(
                                                'sub_invoice_details_id' => $invoice_full->sub_invoice_details_id
                                            )
                                        );
    
                                        var_dump($a_return);
                                        print('<br>');
                                    }
    
                                    print($o_student->personal_data_name);
                                    print('<br>');
                                }
                            }
                        }
                    }
                }
                else {
                    print('fee not found!');
                }
            }
        }
    }

    // function send_short_semester_invoice() {
    //     $mba_invoice_list = $this->Im->get_invoice_list('04',false, [
    //         'di.academic_year_id' => 2022,
    //         'di.semester_type_id' => 2
    //     ]);
    //     if ($mba_invoice_list) {
    //         foreach ($mba_invoice_list as $o_invoice) {
    //             modules::run('callback/api/send_reminder', $o_invoice);
    //             print('send to: '.$o_invoice->personal_data_name);
    //             print('<br>');
    //         }
    //     }
    //     // print('<pre>');var_dump($mba_invoice_list);exit;
    // }

    public function read_ofse_schedule() {
        print('closed!!!');exit;
        $this->load->model('academic/Ofse_model', 'Ofem');
        $s_template_path = APPPATH.'uploads/academic/ofse/OFSE-August-2023/ofse schedule_template_bss.xlsx';
        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();

        $i_row = 2;
        while ($o_sheet->getCell('B'.$i_row)->getValue() !== NULL) {
            $s_student_name = trim($o_sheet->getCell('B'.$i_row)->getValue());
            $s_subject_code = trim($o_sheet->getCell('F'.$i_row)->getValue());
            $s_subject_name = trim($o_sheet->getCell('G'.$i_row)->getValue());

            $mba_student_data = $this->Stm->get_student_filtered(['dpd.personal_data_name' => $s_student_name]);
            if (!$mba_student_data) {
                print($s_student_name.' found!!!');exit;
                // print('<br>');
            }

            $o_student_data = $mba_student_data[0];
            $mba_ofse_data = $this->Ofem->get_ofse_participant_exam_date([
                'sc.student_id' => $o_student_data->student_id,
                'sn.subject_name' => $s_subject_name,
                'sc.ofse_period_id != ' => NULL,
                'cs.curriculum_subject_credit' => 0
            ]);

            // $mba_subject_name = $this->General->get_where('ref_subject_name', ['subject_name' => $s_subject_name]);
            if ($mba_ofse_data) {
                // print('<pre>');var_dump($mba_ofse_data);exit;
                $o_score_ofse = $mba_ofse_data[0];
                $mba_examiner_student = $this->Ofem->get_student_ofse_examiner($o_score_ofse->score_id);
                if (!$mba_examiner_student) {
                    if ($o_sheet->getCell('N'.$i_row)->getValue() !== NULL) {
                        $a_ofse_examiner1_data = [
                            'student_examiner_id' => $this->uuid->v4(),
                            'score_id' => $o_score_ofse->score_id,
                            'advisor_id' => $o_sheet->getCell('N'.$i_row)->getValue(),
                            'examiner_type' => 'examiner_1'
                        ];
                        $this->Ofem->submit_ofse_examiner($a_ofse_examiner1_data);
                    }

                    if ($o_sheet->getCell('P'.$i_row)->getValue() !== NULL) {
                        $a_ofse_examiner2_data = [
                            'student_examiner_id' => $this->uuid->v4(),
                            'score_id' => $o_score_ofse->score_id,
                            'advisor_id' => $o_sheet->getCell('P'.$i_row)->getValue(),
                            'examiner_type' => 'examiner_2'
                        ];
                        $this->Ofem->submit_ofse_examiner($a_ofse_examiner2_data);
                    }
                }
                print(count($mba_ofse_data).' total data!!!');
                print('<br>');
            }

            $i_row++;
        }
    }

    function pdf_show() {
        $parser = new \Smalot\PdfParser\Parser();
        // $s_dir = APPPATH.'uploads/student/2015/HTM/b9720536-77cf-4321-bde9-4c866f88d3cb/thesis_work/Jan_2021_11201510002_thesis_work.pdf';
        // $s_dir = APPPATH.'uploads/student/2019/HTM/f82e85ae-ce4d-4b02-8376-301e9c2773f0/thesis_final/Aug_202311201910001_thesis_final.pdf';
        $s_dir = APPPATH.'uploads/student/2015/INR/a2148a3e-30bc-4f1c-880c-0a5f502780b8/thesis_final/Aug_201911201507008_thesis_final_file.pdf';
        if (file_exists($s_dir)) {
            $pdf = $parser->parseFile($s_dir);
            $detail  = $pdf->getDetails();
            $total_page = $detail['Pages'];
            $abstract_page = 0;
            for ($i=0; $i < $total_page; $i++) { 
                $s_content_page = $pdf->getPages()[$i]->getText();
                $content_lower = strtolower($s_content_page);
                $start_pos_abstract = strpos($content_lower, 'abstract');
                if ($start_pos_abstract > 0) {
                    $abstract_page = $i;break;
                }
            }
            
            // print('<pre>');var_dump($abstract_page);print('<br>');
            // $data = $pdf->getPages()[$abstract_page]->getText();
            // print('<pre>');var_dump($data);exit;

            if ($abstract_page > 0) {
                $abstract = $pdf->getPages()[$abstract_page]->getText();
                $text_lower = strtolower($text);
            }
            
            $a_content = ['<center><b>ABSTRACT</b></center>'];
            $start_pos_abstract = strpos($text_lower, 'abstract') + 9;
            $start_end_pos_abstract = strpos($text_lower, 'keywords');

            $len_content = $start_end_pos_abstract - $start_pos_abstract;
            $text_content = substr($text, $start_pos_abstract, $len_content);
            $text_content = str_replace('=', 'I', $text_content);
            $text_content = str_replace(':', 'H', $text_content);
            $text_content = str_replace("", 'fi', $text_content);
            // array_push($a_content, $text_content);

            // $body_text = urlencode($text_content);
            // $body_text_replace = str_replace('%0A', '___', $body_text);
            // $body_text = urldecode($body_text_replace);
            // $a_body_text = explode('___', $body_text);
            $body_text = nl2br($text_content);
            $a_body_text = explode('<br />', $body_text);
            foreach ($a_body_text as $body) {
                if (!empty(trim($body))) {
                    array_push($a_content, $body);
                }
            }

            $text_end_substring = substr($text, $start_end_pos_abstract);
            $text_end_encode = urlencode($text_end_substring);
            $keyword_text = strstr($text_end_encode, '%0A%0A', true);
            // $keyword_text = strstr($text_end_encode, '%0A', true);
            $keyword_content = urldecode($keyword_text);
            array_push($a_content, '<b>'.$keyword_content.'<b>');

            // $len_end_text = strlen($keyword_content);
            // $end_pos_abstract = $len_end_text + $start_end_pos_abstract;
            // $len_abstract = $end_pos_abstract - $start_pos_abstract;

            // $text_substring = substr($text, $start_pos_abstract, $len_abstract);
            // $result_encode = urlencode($text_substring);
            // $replace = str_replace('%0A%0A', '<br>', $result_encode);
            // $result_decode = urldecode($replace);
            // $a_text_line = explode('\n', $text_substring);
            // $a_text_line = explode('\r\n', $text_substring);
            // foreach ($a_text_line as $key => $value) {
            //     # code...
            // }
            // print('<pre>');var_dump($text_content);exit;
            // echo($text_content);exit;

            $s_filename = 'test.pdf';
            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 11,
                'format' => 'A4-P'
            ]);
            $mpdf->adjustFontDescLineheight = 1.7;
            $mpdf->WriteHTML(implode('<br>', $a_content));
            $s_dir = APPPATH.'uploads/temp/';
            if(!file_exists($s_dir)){
                mkdir($s_dir, 0777, TRUE);
            }
            $mpdf->Output($s_dir.$s_filename, 'F');
            $a_path_info = pathinfo($s_dir.$s_filename);
            $s_mime = mime_content_type($s_dir.$s_filename);
            header("Content-Type: ".$s_mime);
            readfile( $s_dir.$s_filename );
            exit;
        }
        else {
            print('ga ketemu!');
        }
    }

    function lecturer_list_class() {
        $s_employee_id = '59833f8c-7ba3-421b-a4ba-74d3aa8699da';

        // $mba_class_list = $this->Cgm->get_class_master_lecturer(['employee_id' => $s_employee_id]);
        $mba_class_list = $this->Cgm->get_class_master_filtered([
            'cml.employee_id' => $s_employee_id
        ]);
        if ($mba_class_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_filename = 'class_lecturer_all.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_filename)
                ->setCreator("IULI IT Services")
                ->setCategory("Portal System");

            $i_row = 1;
            $o_sheet->setCellValue('A'.$i_row, 'Class Name');
            $o_sheet->setCellValue('B'.$i_row, 'Subject');
            $o_sheet->setCellValue('C'.$i_row, 'Subject Credit');
            $o_sheet->setCellValue('D'.$i_row, 'Semester Academic');
            $o_sheet->setCellValue('E'.$i_row, 'Count Student');
            $i_row++;

            foreach ($mba_class_list as $o_lecturer) {
                $mba_class_subject = $this->Cgm->get_class_master_subject(['cm.class_master_id' => $o_lecturer->class_master_id]);
                // print('<pre>');var_dump($mba_class_subject);exit;
                if ($mba_class_subject) {
                    $o_class_data = $mba_class_subject[0];
                    $mba_class_member = $this->General->get_where('dt_score', [
                        'class_master_id' => $o_lecturer->class_master_id,
                        'score_approval' => 'approved'
                    ]);

                    $o_sheet->setCellValue('A'.$i_row, $o_class_data->class_master_name);
                    $o_sheet->setCellValue('B'.$i_row, $o_class_data->subject_name);
                    $o_sheet->setCellValue('C'.$i_row, $o_class_data->sks);
                    $o_sheet->setCellValue('D'.$i_row, $o_class_data->class_academic_year_id.$o_class_data->class_semester_type_id);
                    $o_sheet->setCellValue('E'.$i_row, (($mba_class_member) ? count($mba_class_member) : '0'));
                    $i_row++;
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    public function defense_to_activity() {
        // print(',');exit;
        $s_academic_year_id = '2023';
        $s_semester_type_id = '2';

        $this->load->model('academic/Activity_study_model', 'Asem');
        $this->load->model('thesis/Thesis_model', 'Tesi');
        
        // $mba_defense = $this->Tesi->get_thesis_defense_student([
        //     'td.academic_year_id' => $s_academic_year_id,
        //     'td.semester_type_id' => $s_semester_type_id
        // ]);

        $mba_defense = $this->Tesi->get_thesis_defense_student([
            'td.thesis_defense_id' => '5394504a-99a1-4459-826f-4f413a6a4bd7',
        ]);

        if ($mba_defense) {
            foreach ($mba_defense as $o_defense) {
                // print('<pre>');var_dump($o_defense);exit;
                $mba_activity_data = $this->Asem->get_activity_data([
                    'das.activity_title' => $o_defense->thesis_title,
                    'das.semester_type_id' => $s_semester_type_id,
                    'das.academic_year_id' => $s_academic_year_id
                ]);
                // print('<pre>');var_dump($mba_activity_data);exit;
                // $mba_student_data = $this->General->get_where('');

                $a_activity_data = [
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'program_id' => 1,
                    'study_program_id' => $o_defense->study_program_id,
                    'id_jenis_aktivitas_mahasiswa' => '22',
                    'activity_member_type' => '0',
                    'activity_title' => $o_defense->thesis_title,
                    'activity_location' => 'Tangerang Selatan'
                ];

                if ($mba_activity_data) {
                    $o_activity = $mba_activity_data[0];
                    print($o_activity->feeder_sync.'-'.$o_activity->activity_title);print("-<br>");
                    // $this->Asem->save_activity_study($a_activity_data, $mba_activity_data[0]->activity_study_id);
                    // print('exists '.$o_activity->activity_study_id);
                    // activity member student
                    
                    $mba_activity_student = $this->Asem->get_activity_student_data([
                        'activity_study_id' => $o_activity->activity_study_id
                    ]);

                    // $a_activity_student_data = [
                    //     'activity_study_id' => $o_activity->activity_study_id,
                    //     'student_id' => $o_defense->student_id,
                    //     'role_type' => '3'
                    // ];
                    // if ($mba_activity_student) {
                        // print('exist '.$mba_activity_student[0]->activity_student_id);
                        // print($mba_activity_student[0]->feeder_sync.'-'.$mba_activity_student[0]->role_type);
                    // }
                    // else {
                    //     // $a_activity_student_data['activity_student_id'] = $this->uuid->v4();
                    //     // $this->Asem->save_student_activity($a_activity_student_data);
                    //     print('insert '.$o_defense->student_id);
                    // }
                    
                    $mba_activity_lecturer = $this->Asem->get_activity_lecturer([
                        'das.activity_study_id' => $o_activity->activity_study_id
                    ]);
                    if ($mba_activity_lecturer) {
                        foreach ($mba_activity_lecturer as $o_activity_lecturer) {
                            print($o_activity_lecturer->feeder_sync.'-'.$o_activity_lecturer->activity_lecturer_type);print('/');
                    //     //     $this->Asem->delete_activity_lecturer($o_activity_lecturer->activity_lecturer_id);
                        }
                        print('ada!');
                    }
                    else {
                    //     $mba_thesis_advisor_student = $this->Tesi->get_list_student_advisor([
                    //         'ts.thesis_student_id' => $o_defense->thesis_students_id
                    //     ], 'advisor', true);
                    //     $mba_thesis_examiner_student = $this->Tesi->get_list_student_advisor([
                    //         'ts.thesis_student_id' => $o_defense->thesis_students_id
                    //     ], 'examiner');
                    //     if ($mba_thesis_advisor_student) {
                    //         // print('<pre>');var_dump($mba_thesis_advisor_student);exit;
                    //         foreach ($mba_thesis_advisor_student as $o_advisor) {
                    //             if (!is_null($o_advisor->employee_id)) {
                    //                 $s_advisor_number = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1), 1);
                    //                 $a_activity_advisor_data = [
                    //                     'activity_lecturer_id' => $this->uuid->v4(),
                    //                     'activity_study_id' => $o_activity->activity_study_id,
                    //                     'id_kategori_kegiatan' => ($s_advisor_number == 1) ? '110403' : '110407',
                    //                     'employee_id' => $o_advisor->employee_id,
                    //                     'activity_lecturer_sequence' => $s_advisor_number,
                    //                     'activity_lecturer_type' => 'adviser'
                    //                 ];
                    //                 // $this->Asem->save_lecturer_activity($a_activity_advisor_data);
                    //                 print('insert '.$o_activity->activity_study_id.' advisor '.$o_advisor->personal_data_name.'<br>');
                    //             }
                    //         }
                    //     }

                    //     if ($mba_thesis_examiner_student) {
                    //         // print('<pre>');var_dump($mba_thesis_examiner_student);exit;
                    //         foreach ($mba_thesis_examiner_student as $o_examiner) {
                    //             if (!is_null($o_examiner->employee_id)) {
                    //                 $s_examiner_number = substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1), 1);
                    //                 $a_activity_examiner_data = [
                    //                     'activity_lecturer_id' => $this->uuid->v4(),
                    //                     'activity_study_id' => $o_activity->activity_study_id,
                    //                     'id_kategori_kegiatan' => ($s_examiner_number == 1) ? '110501' : '110502',
                    //                     'employee_id' => $o_examiner->employee_id,
                    //                     'activity_lecturer_sequence' => $s_examiner_number,
                    //                     'activity_lecturer_type' => 'examiner'
                    //                 ];
                    //                 // $this->Asem->save_lecturer_activity($a_activity_examiner_data);
                    //                 print('insert '.$o_activity->activity_study_id.' examiner '.$o_examiner->personal_data_name.'<br>');
                    //             }
                    //         }
                    //     }
                    //     // $mba_thesis_examiner_student;
                        print('ga ada!');
                    }
                    // // 110403: advisor1
                    // // : advisor2
                    // // : examiner1
                    // // : examiner2345678


                    // // activity member advisor
                    // // activity member examiner
                    // // print('exists '.$o_defense->thesis_title);
                }
                else {
                    $a_activity_data['activity_study_id'] = $this->uuid->v4();
                    $this->Asem->save_activity_study($a_activity_data);
                    $sync_data = modules::run('feeder/activity/sync_activity', $a_activity_data['activity_study_id']);
                    $s_id_aktivitas = $sync_data['id_aktivitas'];

                    print('insert activity '.$s_id_aktivitas);
                    print('-<br>');

                    $s_activity_student_id = $this->uuid->v4();
                    $a_actstudent_data = [
                        'activity_student_id' => $s_activity_student_id,
                        'activity_study_id' => $s_id_aktivitas,
                        'student_id' => $o_defense->student_id,
                        'role_type' => '3'
                    ];
    
                    if ($this->Asem->save_student_activity($a_actstudent_data)) {
                        $dikti_sync = modules::run('feeder/activity/sync_peserta_aktivitas', $s_activity_student_id);
                    }
                    print('insert student '.$s_activity_student_id);
                    print('-<br>');

                    $mba_advisor_data = $this->Tesi->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_defense->thesis_students_id
                    ], 'advisor', true);
                    $mba_examiner_data = $this->Tesi->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_defense->thesis_students_id
                    ], 'examiner');

                    if ($mba_advisor_data) {
                        foreach ($mba_advisor_data as $o_advisor) {
                            $s_activity_lecturer_id = $this->uuid->v4();
                            $sequence = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1), 1);
                            $a_data = [
                                'activity_lecturer_id' => $s_activity_lecturer_id,
                                'activity_study_id' => $s_id_aktivitas,
                                'id_kategori_kegiatan' => ($o_advisor->advisor_type == 'approved_advisor_1') ? '110403' : '110407',
                                'employee_id' => $o_advisor->employee_id,
                                'activity_lecturer_sequence' => $sequence,
                                'activity_lecturer_type' => 'adviser',
                            ];
                            if ($this->Asem->save_lecturer_activity($a_data)) {
                                modules::run('feeder/activity/sync_pembimbing_aktvitas', $s_activity_lecturer_id);
                            }
                            print('insert advisor '.$o_advisor->personal_data_name.'-adviser/');
                        }
                    }
    
                    if ($mba_examiner_data) {
                        foreach ($mba_examiner_data as $o_examiner) {
                            $s_activity_lecturer_id = $this->uuid->v4();
                            $sequence = substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1), 1);
                            $a_data = [
                                'activity_lecturer_id' => $s_activity_lecturer_id,
                                'activity_study_id' => $s_id_aktivitas,
                                'id_kategori_kegiatan' => ($o_examiner->examiner_type == 'examiner_1') ? '110501' : '110502',
                                'employee_id' => $o_examiner->employee_id,
                                'activity_lecturer_sequence' => $sequence,
                                'activity_lecturer_type' => 'examiner',
                            ];
                            if ($this->Asem->save_lecturer_activity($a_data)) {
                                modules::run('feeder/activity/sync_penguji_aktvitas', $s_activity_lecturer_id);
                            }
                            print('insert advisor '.$o_examiner->personal_data_name.'-examiner/');
                        }
                    }
                    // print('not exists '.$o_defense->personal_data_name.' / '.$o_defense->thesis_title);
                }
                print('<br>');
            }
        }
    }

    function get_invoice_student_selected() {
        $a_scholarship_sml = ['f0007fb7-b81b-11e9-849d-5254005d90f6','9106adad-56ab-11ea-8aee-5254005d90f6'];
        $a_personal_data_name = ['ABANG MUHAMMAD HANGGARA PUTRA','ABELARD RICHARD FLANAGAN','Abraham Mahanaim','Adesurya Budi Teresianto','ADI ARISYAHPUTRA','AKURSIO CHANDRASURYA PANNADITTA YANGSEN','ANDI ICHDINAVIA NOURIKA','ANDIRSON GAWO MATA','ANRIKO HESYA PRAMUDYA','ARMAND TRISTAN DJAJANEGARA','DANISH NURIKHSAN','DAVID RIVENDELL WALALANGI','ERIN LEE SUSANTO','EUGENIUS FABIAN SETIA ATMADJA','FRANSISCUS DARRYL GUMULIA','Hanif Mulya Pratama','HUSEIN Al-Hinduan','Imaji Akbar Pasya Lasahido','IVAN JOE','KEVIN RAHANGIAR','KEZIA ANDJANIE GRISELLA','LARAS DWI PUTRI ARIANTI','Luthfi Kamal Enditra','M BIMO ADJIE ATHALLAH','Mario Julian Prakoso','MATTHEW DARRELL KURNIAWAN','MUHAMMAD AFFAN','MUHAMMAD ANARGYA BINTANG AGUNG','MUHAMMAD RIZKY PERMANA','REGAN LEONARD','RENGGANI GHIFARI','Ressy Narita','SALMA AZIIZAH NOVRIAN','SAYIDINA ALI RIDHA ATHALLAH','TAFTREJ GERSOM KUSUMO','WARANEY IMMANUEL DENDENG','Willmar Haposan Sihombing','ZAID HABIBIE HIDAYAT'];
        print('<h3>Semester 2020-1</h3>');
        print('<table border="1" style="width:100%">');
        print('<tr>');
        print('<td>No</td>');
        print('<td>Student Name</td>');
        print('<td>Invoice Number</td>');
        print('<td>Invoice Semester</td>');
        print('<td>Academic Semester</td>');
        print('<td>Current Student Status</td>');
        print('<td>Fee Description</td>');
        print('<td>Billing Amount</td>');
        print('<td>Additional Billing</td>');
        print('<td>Discount List</td>');
        print('<td>Total Discount</td>');
        print('<td>Total Billed</td>');
        print('<td>Total Paid</td>');
        print('<td>Invoice Created Datetime</td>');
        print('</tr>');
        $no = 1;
        foreach ($a_personal_data_name as $s_name) {
            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_name' => $s_name
            ]);
            // if (!$mba_student_data) {
            //     print($s_name);exit;
            // }
            $o_student = $mba_student_data[0];
            $mba_invoice_student = $this->Im->get_invoice_list_detail([
                'fee.payment_type_code' => '02',
                'di.personal_data_id' => $o_student->personal_data_id,
                'di.invoice_status != ' => 'cancelled'
            ]);
            if ($mba_invoice_student) {
                foreach ($mba_invoice_student as $o_invoice) {
                    $mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_invoice->semester_id]);
                    $mba_invoice_details = $this->Im->get_invoice_details([
                        'did.invoice_id' => $o_invoice->invoice_id,
                        'df.fee_amount_type != ' => 'main'
                    ]);
                    $mba_invoice_data = $this->Im->get_invoice_data([
                        'di.invoice_id' => $o_invoice->invoice_id
                    ]);
                    $mba_student_semester = $this->General->get_where('dt_student_semester', [
                        'student_id' => $o_student->student_id,
                        'semester_id' => $o_invoice->semester_id
                    ]);
                    $s_additional = '';
                    $a_additional = [];
                    $s_additional_discount = '';
                    $a_additional_discount = [];
                    $d_fee_amount = $o_invoice->invoice_details_amount;
                    $d_total_discount = 0;
                    $d_total_additional = 0;
                    $s_fee_description = $o_invoice->fee_description;

                    if (in_array($o_invoice->fee_scholarship_id, $a_scholarship_sml)) {
                        $mba_fee_unscholarship = $this->General->get_where('dt_fee', [
                            'payment_type_code' => '02',
                            'scholarship_id' => NULL,
                            'study_program_id' => $o_invoice->study_program_id,
                            'semester_id' => $o_invoice->semester_id,
                            'academic_year_id' => $o_invoice->academic_year_id,
                            'fee_amount_type' => 'main'
                        ]);
                        $d_before_amount = $d_fee_amount;
                        $d_fee_amount = ($mba_fee_unscholarship) ? $mba_fee_unscholarship[0]->fee_amount : $d_fee_amount;
                        $d_discount_amount = $d_fee_amount - $d_before_amount;
                        $d_total_discount += $d_discount_amount;
                        $s_discount_amount = '- Rp. '.number_format($d_total_discount, 0, '.', '.');
                        array_push($a_additional_discount, 'SML Scholarship: '.$s_discount_amount);
                        $s_fee_description = ($mba_fee_unscholarship) ? $mba_fee_unscholarship[0]->fee_description : $s_fee_description;
                    }

                    if ($mba_invoice_details) {
                        foreach ($mba_invoice_details as $o_detail) {
                            // print('<pre>');var_dump($o_detail);exit;
                            // $s_additional .= ';'.$o_detail->fee_description;
                            $d_detail_amount = number_format($o_detail->invoice_details_amount, 0, '.', '.');
                            $s_detail_type = ($o_detail->invoice_details_amount_number_type == 'number') ? 'Rp. '.$d_detail_amount : $d_detail_amount.'%';
                            $s_detail_sign = ($o_detail->invoice_details_amount_sign_type == 'positive') ? $s_detail_type : '- '.$s_detail_type;
                            if ($o_detail->invoice_details_amount_sign_type == 'positive') {
                                array_push($a_additional, $o_detail->fee_description.': '.$s_detail_sign);
                                if ($o_detail->invoice_details_amount_number_type == 'number') {
                                    $d_total_additional += $o_detail->invoice_details_amount;
                                }
                                else {
                                    $d_fee_amount_additional = ($d_fee_amount * $o_detail->invoice_details_amount) / 100;
                                    $d_total_additional += $d_fee_amount_additional;
                                }
                            }
                            else {
                                array_push($a_additional_discount, $o_detail->fee_description.': '.$s_detail_sign);
                                if ($o_detail->invoice_details_amount_number_type == 'number') {
                                    $d_total_discount += $o_detail->invoice_details_amount;
                                }
                                else {
                                    $d_fee_amount_additional = ($d_fee_amount * $o_detail->invoice_details_amount) / 100;
                                    $d_total_discount += $d_fee_amount_additional;
                                }
                            }
                        }
                    }

                    $d_total_paid = 0;
                    if ($mba_invoice_data) {
                        foreach ($mba_invoice_data as $o_invoice_sub_details) {
                            if ($o_invoice_sub_details->sub_invoice_details_amount_paid > 0) {
                                $d_total_paid += $o_invoice_sub_details->sub_invoice_details_amount_paid;
                            }
                        }
                    }
                    $s_fee_amount = number_format($d_fee_amount, 0, '.', '.');
                    $s_total_discount = number_format($d_total_discount, 0, '.', '.');
                    $d_total_billed = $d_fee_amount + $d_total_additional - $d_total_discount;
                    $s_total_billed = number_format($d_total_billed, 0, '.', '.');
                    $s_additional = (empty($a_additional)) ? '' : '<li>'. implode('</li><li>', $a_additional).'</li>';
                    $s_additional_discount = (empty($a_additional_discount)) ? '' : '<li>'. implode('</li><li>', $a_additional_discount).'</li>';
                    $s_total_paid = number_format($d_total_paid, 0, '.', '.');
                    print('<tr>');
                    print('<td>'.$no++.'</td>');
                    print('<td>'.$o_student->personal_data_name.'</td>');
                    print('<td>'.$o_invoice->invoice_number.'</td>');
                    print('<td>'.$mba_semester_data[0]->semester_number.'</td>');
                    print('<td>'.(($mba_student_semester) ? $mba_student_semester[0]->academic_year_id.$mba_student_semester[0]->semester_type_id : '').'</td>');
                    print('<td>'.$o_student->student_status.'</td>');
                    print('<td>'.$s_fee_description.'</td>');
                    print('<td>'.$s_fee_amount.'</td>');
                    print('<td>'.$s_additional.'</td>');
                    print('<td>'.$s_additional_discount.'</td>');
                    print('<td>'.$s_total_discount.'</td>');
                    print('<td>'.$s_total_billed.'</td>');
                    print('<td>'.$s_total_paid.'</td>');
                    print('<td>'.date('d F Y H:i:s', strtotime($o_invoice->date_added)).'</td>');
                    print('</tr>');
                }
            }
        }
        print('</table>');
    }

    function get_invoice_semester_by_student($s_academic_semester) {
        // $a_academic_semester = explode('-', $s_academic_semester);
        $s_academic_year_id = substr($s_academic_semester, 0, 4);
        $s_semester_type_id = substr($s_academic_semester, 4, 1);
        // print($s_semester_type_id);exit;

        print('<h3>Semester 2020-1</h3>');
        print('<table border="1" style="width:100%">');
        print('<tr>');
        print('<td>No</td>');
        print('<td>Student Name</td>');
        print('<td>Semester</td>');
        print('<td>Billing Amount</td>');
        print('<td>Discount List</td>');
        print('<td>Total Discount</td>');
        print('<td>Additional Billing</td>');
        print('<td>Total Paid</td>');
        print('<td>Invoice Created</td>');
        print('</tr>');

        $mba_student_semester = $this->General->get_where('dt_student_semester', [
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);
        if ($mba_student_semester) {
            $no = 1;
            foreach ($mba_student_semester as $o_student_semester) {
                if (is_null($o_student_semester->semester_id)) {
                    print('semester_id kosong student_id'.$o_student_semester->student_id);exit;
                }
                $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_student_semester->student_id]);
                if ($mba_student_data) {
                    $o_student = $mba_student_data[0];
                    $mba_invoice_student = $this->Im->get_invoice_list_detail([
                        'fee.payment_type_code' => '02',
                        'di.personal_data_id' => $o_student->personal_data_id,
                        'fee.semester_id' => $o_student_semester->semester_id,
                        'di.invoice_status != ' => 'cancelled'
                    ]);
                    if ($mba_invoice_student) {
                        foreach ($mba_invoice_student as $o_invoice) {
                            print('<tr>');
                            print('<td>'.$no++.'</td>');
                            print('<td>'.$o_student->personal_data_name.'</td>');
                            print('<td>'.$o_invoice->semester_id.'</td>');
                            print('<td>'.$o_invoice->invoice_details_amount.'</td>');
                            print('<td>'.$o_invoice->fee_description.'</td>');
                            print('<td>'.$o_invoice->fee_amount.'</td>');
                            print('<td>'.$o_invoice->fee_amount_type.'</td>');
                            print('<td>'.$o_invoice->invoice_amount_paid.'</td>');
                            print('<td>'.date('d F Y H:i:s', strtotime($o_invoice->date_added)).'</td>');
                            print('</tr>');
                        }
                    }
                }
                else {
                    print('student not found!<pre>');var_dump($o_student_semester);exit;
                }
            }
        }
        print('</table>');
        // print('<pre>');var_dump($mba_student_semester);exit;
    }

    function get_invoice_semester() {
        // semester 2020 ganjil (2020-06-01 / 2020-07-30)
        $invoice_list = $this->Im->get_invoice_by_deadline([
            'DATE(di.date_added) >= ' => '2020-06-01',
            'DATE(di.date_added) <= ' => '2020-07-30'
        ], '02', false);
        // print('<pre>');var_dump($invoice_list);exit;
        print('<h3>Semester 2020-1</h3>');
        print('<table border="1" style="width:100%">');
        print('<tr>');
        print('<td>No</td>');
        print('<td>Student Name</td>');
        print('<td>Semester</td>');
        print('<td>Billing Amount</td>');
        print('<td>Discount List</td>');
        print('<td>Total Discount</td>');
        print('<td>Additional Billing</td>');
        print('<td>Total Paid</td>');
        print('</tr>');
        if ($invoice_list) {
            $no = 1;
            foreach ($invoice_list as $o_invoice) {
                $mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id]);
                if ($mba_student_data) {
                    $o_student = $mba_student_data[0];
                    print('<tr>');
                    print('<td>'.$no++.'</td>');
                    print('<td>'.$o_student->personal_data_name.'</td>');
                    print('<td>'.$o_invoice->semester_id.'</td>');
                    print('<td>'.$o_invoice->invoice_details_amount.'</td>');
                    print('<td>'.$o_invoice->fee_description.'</td>');
                    print('<td>'.$o_invoice->fee_amount.'</td>');
                    print('<td>'.$o_invoice->fee_amount_type.'</td>');
                    print('<td>'.$o_invoice->invoice_amount_paid.'</td>');
                    print('</tr>');
                }
                else {
                    print('Student not found!<pre>');var_dump($o_invoice);exit;
                }
            }
        }
        print('</table>');
    }

    function submit_student_thesis_file_form($s_student_id)
    {
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
        if ($mba_student_data) {
            $this->a_page_data['student_data'] = $mba_student_data[0];
            $this->a_page_data['list_filetype'] = $this->General->get_enum_values('thesis_students_file', 'thesis_filetype');
            $this->a_page_data['body'] = $this->load->view('thesis/form/form_upload_all_file', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    function repair_file_thesis() {
        exit;
        // $s_path = APPPATH."uploads/temp/thesis_file_temp/";
        $s_path = APPPATH."uploads/temp/thesis_file_temp/2019-2020/";
        $listdir = scandir($s_path);
        $a_folder_bypass = ['.', '..', 'desktop.ini'];
        // work, final, work_pl, final_other, work_other, work_log,  
        foreach ($listdir as $s_folder) {
            if (!in_array($s_folder, $a_folder_bypass)) {
                $a_folder_structure = explode('-', $s_folder);
                $s_student_number = trim($a_folder_structure[1]);

                $mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number]);
                if (!$mba_student_data) {
                    print($s_folder);exit;
                }
                $o_student = $mba_student_data[0];
                // $s_new_path = STUDENTPATH.$o_student->personal_data_path;
                if (!is_null($o_student->student_date_graduated)) {
                    $s_file = date('M', strtotime($o_student->student_date_graduated)).'_'.date('Y', strtotime($o_student->student_date_graduated)).$o_student->student_number.'_';
                }
                else {
                    $s_file = date('M').'_'.date('Y').$o_student->student_number.'_';
                }

                $s_new_path = STUDENTPATH.$o_student->academic_year_id.'/'.$o_student->study_program_abbreviation.'/'.$o_student->student_id.'/';
                $isidir = scandir($s_path.$s_folder.'/');
                foreach ($isidir as $filename) {
                    if (!in_array($filename, $a_folder_bypass)) {
                        if (is_dir($s_path.$s_folder.'/'.$filename)) {
                            $sub_dir = scandir($s_path.$s_folder.'/'.$filename.'/');
                            foreach ($sub_dir as $sub_file) {
                                if (!in_array($sub_file, $a_folder_bypass)) {
                                    print('ini_sub_'.$s_folder.'/'.$filename.'/'.$sub_file);
                                    print('<br>');
                                }
                            }
                        }
                        else {
                            $s_keycode = explode('_', $filename)[0];
                            $s_thesis_filetype = NULL;
                            $s_type = NULL;
                            switch ($s_keycode) {
                                case 'proposal':
                                    $s_thesis_filetype = 'thesis_proposal_file';
                                    $s_type = 'proposal';
                                    break;
                                case 'work':
                                    $s_thesis_filetype = 'thesis_work_file';
                                    $s_type = 'work';
                                    break;
                                case 'final':
                                    $s_thesis_filetype = 'thesis_final_file';$s_type = 'final';
                                    break;
                                case 'workpl':
                                    $s_thesis_filetype = 'thesis_work_plagiate_check';$s_type = 'work';
                                    break;
                                case 'finalother':
                                    $s_thesis_filetype = 'thesis_final_other_doc';$s_type = 'final';
                                    break;
                                case 'workother':
                                    $s_thesis_filetype = 'thesis_work_other_doc';$s_type = 'work';
                                    break;
                                case 'worklog':
                                    $s_thesis_filetype = 'thesis_work_log';$s_type = 'work';
                                    break;
                                default:
                                    break;
                            }

                            if ((!is_null($s_thesis_filetype)) AND (!is_null($s_type))) {
                                $mba_student_file_thesis = $this->Tsm->get_list_thesis_file([
                                    'ts.student_id' => $o_student->student_id,
                                    'sf.thesis_filetype' => $s_thesis_filetype
                                ]);
                                if (!$mba_student_file_thesis) {
                                    $mba_thesis_student = $this->General->get_where('thesis_students', ['student_id' => $o_student->student_id]);
                                    if (!$mba_thesis_student) {
                                        $s_thesis_student_id = '-';
                                        print('kosong');exit;
                                    }
                                    else {
                                        $s_thesis_student_id = $mba_thesis_student[0]->thesis_student_id;
                                        $mba_thesis_log = $this->General->get_where('thesis_students_log_status', [
                                            'thesis_student_id' => $s_thesis_student_id,
                                            'thesis_log_type' => $s_type
                                        ]);
                                        if (!$mba_thesis_log) {
                                            print('kosooong');exit;
                                            // $log_id = $this->uuid->v4();
                                            // $this->General->insert_data('thesis_students_log_status', [
                                            //     'thesis_log_id' => $log_id,
                                            //     'thesis_student_id' => $s_thesis_student_id,
                                            //     'academic_year_id' => '2018',
                                            //     'semester_type_id' => '2',
                                            //     'thesis_log_type' => $s_type,
                                            //     'thesis_status' => 'approved',
                                            //     'date_added' => date('Y-m-d H:i:s')
                                            // ]);
                                        }
                                        else {
                                            $log_id = $mba_thesis_log[0]->thesis_log_id;
                                            $s_newpath = $s_new_path.'thesis_'.$s_type.'/';
                                            if(!file_exists($s_newpath)){
                                                mkdir($s_newpath, 0777, TRUE);
                                            }
                                            
                                            $s_filename = $s_file.$s_thesis_filetype;
                                            $a_filedata = explode('.', $filename);
                                            $s_extent = $a_filedata[count($a_filedata) - 1];
                                            $s_filename .= '.'.$s_extent;
                                            print($s_path.$s_folder.'/'.$filename);print('<br>');
                                            print('-->'.$s_newpath.$s_filename);print('<br>');print('<br>');
                                            $renamepath = rename($s_path.$s_folder.'/'.$filename, $s_newpath.$s_filename);
                                            if ($renamepath) {
                                                $this->General->insert_data('thesis_students_file', [
                                                    'thesis_file_id' => $this->uuid->v4(),
                                                    'thesis_log_id' => $log_id,
                                                    'thesis_filetype' => $s_thesis_filetype,
                                                    'thesis_filename' => $s_filename,
                                                    'date_added' => date('Y-m-d H:i:s')
                                                ]);
                                            }
                                        }
                                    }
                                    // print('isi_'.$log_id.'_');
                                }
    
                                // print($s_folder.' / '.$filename);
                                // print('<br>');
                            }
                        }
                    }
                }
            }
        }
        // print('<pre>');var_dump($listdir);exit;
    }

    function repair_student_path() {
        $mainpath = APPPATH.'uploads/student/';
        $list_mainpath = scandir($mainpath);
        $a_folder_bypass = ['.', '..', 'thesis_proposal', 'thesis_revision', 'thesis_work','Apr','Jan','Mar','Oct'];
        $a_list_folder_student_id = [];
        foreach ($list_mainpath as $s_mainfolder) {
            if (!in_array($s_mainfolder, $a_folder_bypass)) {
                $diryear = $mainpath.$s_mainfolder.'/';
                $list_diryear = scandir($diryear);
                foreach ($list_diryear as $year_folder) {
                    if (!in_array($year_folder, $a_folder_bypass)) {
                        $dirprodi = $diryear.$year_folder.'/';
                        $list_dirprodi = scandir($dirprodi);
                        foreach ($list_dirprodi as $prodi_folder) {
                            if (!in_array($prodi_folder, $a_folder_bypass)) {
                                $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $prodi_folder]);
                                if ($mba_student_data) {
                                    $o_student = $mba_student_data[0];
                                    $dir_isiprodi = $dirprodi.$prodi_folder.'/';
                                    $list_isiprodi = scandir($dir_isiprodi);
                                    foreach ($list_isiprodi as $isifolder) {
                                        if (!in_array($isifolder, $a_list_folder_student_id)) {
                                            array_push($a_list_folder_student_id, $isifolder);
                                        }
                                    }
                                    // $a_list_folder_student_id = array_merge($a_list_folder_student_id, $list_isiprodi);
                                    $s_isiprodi = implode('|', $list_isiprodi);
                                    print($dirprodi.$prodi_folder.' => '.$o_student->personal_data_name.' ['.$s_isiprodi.']');print('<br>');
                                }
                                else {
                                    $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $prodi_folder]);
                                    if ($mba_personal_data) {
                                        print($dirprodi.$prodi_folder.' => '.$mba_personal_data[0]->personal_data_name.' __ Personal Data');print('<br>');
                                    }
                                    else {
                                        print($dirprodi.$prodi_folder.' => No Student ID');print('<br>');
                                    }
                                }
                                // print($dirprodi.$prodi_folder);print('<br>');
                            }
                        }
                    }
                }
            }
        }

        print('<pre>');var_dump($a_list_folder_student_id);
    }

    function set_session_semester($s_academic_year_id, $s_semester_type_id) {
        $this->session->set_userdata('academic_year_id_active', $s_academic_year_id);
        $this->session->set_userdata('semester_type_id_active', $s_semester_type_id);
    }

    function show_path() {
        print('STUDENTPATH: '.STUDENTPATH.'<br>');
        print('APPPATH: '.APPPATH.'<br>');
        print('BASEPATH: '.BASEPATH.'<br>');
    }

    function fixed_student_path() {
        $mba_student_data = $this->Stm->get_student_filtered([]);
        foreach ($mba_student_data as $o_student) {
            if (is_null($o_student->personal_data_path)) {
                $s_student_path = $this->Stm->get_student_path(['st.student_id' => $o_student->student_id]);
                if ($s_student_path) {
                    $s_student_path = str_replace(STUDENTPATH, '', $s_student_path);
                    $this->General->update_data('dt_personal_data', ['personal_data_path' => $s_student_path], [
                        'personal_data_id' => $o_student->personal_data_id
                    ]);
                    print($o_student->personal_data_name.' => '.$s_student_path);
                    print('<br>');
                }
            }
        }
    }

    function move_file_thesis($a_data) {
        $a_data = [
            'student_id' => 'f40dbe0c-c71c-4416-be67-931c0e087032',
            'filepath' => APPPATH.'uploads/temp/thesis_file_temp/2019-2020/AMALIYA FARDIYANI - 11201506002/'
        ];

        if (file_exists($a_data['filepath'])) {
            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $a_data['student_id']]);
            $o_student = $mba_student_data[0];
            $s_student_path = $this->Stm->get_student_path(['st.student_id' => $o_student->student_id]);
            if ($s_student_path) {
                if (!file_exists($a_data['filepath'])) {
                    mkdir($s_file_path, 0777, TRUE);
                }
            }
            else {
                print('error retrieve student path!'.$o_student->student_id);
            }
        }
        else {
            print('path not found! - '.$a_data['filepath']);
        }
    }

    function get_tracer_result_question() {
        $a_graduate_year_allowed = ['2023', '2022', '2021', '2020', '2019'];
        // $s_question_id = 'f5'; // waktu tunggu lulusan
        // $s_question_id = 'f14'; // keeratan prodi dengan kerjaan
        $s_question_id = 'f4'; // f8, f11, iuli1, status pekerjaan
        $a_prodi_in = ['01a781d9-81cd-11e9-bdfc-5254005d90f6', 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32', 'ed375a1a-81cc-11e9-bdfc-5254005d90f6'];
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_status' => 'graduated'], false, false, $a_prodi_in);
        // print('<pre>');var_dump($mba_student_data);exit;
        if ($mba_student_data) {
            print('<table>');
            print('<tr>');
            print('<th>Student Name</th>');
            print('<th>Prodi</th>');
            print('<th>Batch</th>');
            print('<th>Graduation Year</th>');
            print('<th>Graduation Year PDDikti</th>');
            print('<th>Answer</th>');
            print('<th>Answer Value</th>');
            print('</tr>');
            foreach ($mba_student_data as $o_student) {
                if (in_array($o_student->graduated_year_id, $a_graduate_year_allowed)) {
                    $mba_question_answer = $this->General->get_where('dikti_question_answers', [
                        'personal_data_id' => $o_student->personal_data_id,
                        'question_id' => $s_question_id
                    ]);
                    if (($mba_question_answer) AND (count($mba_question_answer) > 1)) {
                        print('lebih dari satu jawaban <pre>');var_dump($mba_question_answer);exit;
                    }
                    else if ($mba_question_answer) {
                        $o_question_answer = $mba_question_answer[0];
                        $question_choice_data = $this->General->get_where('dikti_question_choices', [
                            'question_choice_id' => $o_question_answer->question_section_id
                        ]);

                        print('<tr>');
                        print('<td>'.$o_student->personal_data_name.'</td>');
                        print('<td>'.$o_student->study_program_name_feeder.'</td>');
                        print('<td>'.$o_student->academic_year_id.'</td>');
                        print('<td>'.$o_student->graduated_year_id.'</td>');
                        print('<td>'.(intval($o_student->graduated_year_id) - 1).'</td>');
                        print('<td>'.$question_choice_data[0]->question_choice_name.'</td>');
                        print('<td>'.$o_question_answer->answer_content.'</td>');
                        print('</tr>');
                    }
                }
            }
            print('</table>');
            // print('woow');
            // print('<pre>');var_dump($mba_student_data);exit;
        }
    }

    function get_biodata_student_data() {
        $this->load->model('address/Address_model', 'Add');
        $a_student_number = ['11201702002','11201702012','11201802006','11201802009','11201902011','11201902015','11202002001','11202002004','11202002005','11202002006','11202002011','11202002015',
            '11202102002','11202102003','11202102005','11202102007','11202102008','11202102009','11202102010','11202102011','11202102012','11202102014','11202102015','11202102016',
            '11202202001','11202202002','11202202003','11202202005','11202202007','11202202009','11202202011','11202202012','11202202013','11201904002','11201904007','11202004002',
            '11202204001','11202204002','11202204003','11202204004','11201812007','11202012003','11202112001','11202112002','11202212002','11202212004','11201806009','11202106002',
            '11202106003','11202106004','11202106006','11202206001','11202206002','11202206004','11201803004','11201803007','11201803008','11201803010','11201903003','11201903006',
            '11201903008','11202003001','11202003003','11202203001','11201701007','11201801007','11201901001','11201901010','11201901013','11201901016','11202001001','11202001002',
            '11202001003','11202001006','11202001016','11202001019','11202101001','11202101003','11202101004','11202101007','11202101008','11202101009','11202101010','11202101012',
            '11202101013','11202201002','11202201003','11202201004','11202201005','11202201008','11202201009','11202201010','11202201011','11201705006','11202005003','11202005005',
            '11202205002','11202101005','11201602002','11201602025','11201902001','11201902005','11201902007','11201902008','11201902012','11201902013','11201902014','11201604004',
            '11201904003','11201904006','11201912005','11201912009','11201912011','11201806002','11201806003','11201806005','11201906002','11201906003','11201703008','11201803011',
            '11201813002','11201813004','11201601024','11201801002','11201901002','11201901004','11201901006','11201901018','11201901020','11202001004','11201905001','11201905002',
            '11201902004','11201912010','11201906001','11201903001','11201903004','11201502003','11201502014','11201502026','11201502034','11201502040','11201502041','11201602004',
            '11201602008','11201602013','11201602023','11201602030','11201602031','11201602032','11201702007','11201702008','11201702009','11201702015','11201702017','11201802007',
            '11201802011','11201802015','11201802016','11201802018','11201802019','11202102006','11202102013','11202202014','11201504007','11201604003','11201604006','11201604009',
            '11201704002','11201704005','11201904004','11201904005','11201712002','11201712004','11201806007','11201906005','11201503004','11201503006','11201603007','11201703003',
            '11201703005','11201813001','11202003007','11202203003','11201501034','11201601005','11201601008','11201601014','11201601018','11201601027','11201701001','11201701003',
            '11201701008','11201701013','11201701017','11201701019','11201701021','11201801010','11201801012','11201801016','11201801020','11201801022','11201801023','11201801024',
            '11201801025','11201801026','11201801027','11201801030','11201801031','11201901007','11201901008','11201901009','11201901011','11201901015','11201901019','11201901021',
            '11201902009','11202001005','11202001018','11202101006','11202101011','11202201001','11202201006','11202201007','11201505006','11201705002','11201805001','11201805003','11202005002','11202005006','11202205001'];

        $number = 0;
        print('<table border="1">');
        print('<tr><td>No</td><td>Student Name</td><td>Student Number</td><td>Prodi</td><td>Citizenship</td><td>Negara</td><td>Kota</td><td>Wilayah</td><td>Gender</td><td>Perkerjaan Orang Tua</td></tr>');
        foreach ($a_student_number as $s_student_number) {
            $number++;
            $mba_student_filtered = $this->Stm->get_student_filtered([
                'ds.student_number' => $s_student_number
            ]);
            if ($mba_student_filtered) {
                $o_student = $mba_student_filtered[0];
                $s_negara_asal = '';
                $s_kota_asal = '';
                $s_wilayah_asal = '';
                $s_parent_ocupation = '';

                if (!is_null($o_student->family_id)) {
                    $mba_parent_data = $this->Fmm->get_family_lists_filtered([
                        'fmm.family_id' => $o_student->family_id,
                        'fmm.family_member_status != ' => 'child'
                    ]);
                    if ($mba_parent_data) {
                        $s_parent_ocupation = $mba_parent_data[0]->ocupation_name;
                    }
                }
                $mba_personal_address = $this->Add->get_personal_address($o_student->personal_data_id);
                if ($mba_personal_address) {
                    $s_negara_asal = $mba_personal_address[0]->country_name;
                    $s_kota_asal = $mba_personal_address[0]->address_city;
                    $s_wilayah_asal = $mba_personal_address[0]->nama_wilayah;
                }
                print('<tr>');
                print('<td>'.$number.'</td>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td>'.$o_student->student_number.'</td>');
                print('<td>'.$o_student->study_program_name_feeder.'</td>');
                print('<td>'.$o_student->citizenship_country_name.'</td>');
                print('<td>'.$s_negara_asal.'</td>');
                print('<td>'.$s_kota_asal.'</td>');
                print('<td>'.$s_wilayah_asal.'</td>');
                print('<td>'.$o_student->personal_data_gender.'</td>');
                print('<td>'.$s_parent_ocupation.'</td>');
                print('</tr>');
            }
            else {
                print($s_student_number.' not found!'.'<br>');
            }
        }
        print('</table>');
    }

    public function short_semester_invoice()
    {
        print('closed!cek mail to');exit;
        $s_academic_year_id = 2022;
        $s_semester_type_id = 8;

        $mba_score_data = $this->Scm->get_student_by_score([
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id,
            'sc.score_approval' => 'approved'
        ]);

        if ($mba_score_data) {
            $i_count = 0;
            foreach ($mba_score_data as $o_student) {
                $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_student->semester_id])[0];

                $mbo_fee_data = $this->Im->get_fee([
                    'semester_id' => $o_student->semester_id,
                    'payment_type_code' => '04',
                    'program_id' => $o_student->program_id,
                    'study_program_id' => $o_student->study_program_id,
                    'academic_year_id' => $o_student->finance_year_id,
                    'fee_amount_type' => 'main'
                ])[0];

                if ($mbo_fee_data) {
                    $mba_score_student = $this->Scm->get_score_data_transcript([
                        'sc.student_id' => $o_student->student_id,
                        'sc.academic_year_id' => $s_academic_year_id,
                        'sc.semester_type_id' => $s_semester_type_id,
                        'sc.score_approval' => 'approved'
                    ]);

                    if (!$mba_score_student) {
                        print('Score '.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number.' ga ada!<br>');
                    }else{
                        $i_count_subject = 0;
                        $a_score_id = [];
                        $a_sks = [];
                        $s_subject_list = '';
                        foreach ($mba_score_student as $o_score) {
                            $d_subject_fee = $mbo_fee_data->fee_amount * $o_score->curriculum_subject_credit;
                            $d_subject_fee = number_format($d_subject_fee, 0, ',', '.');
                            array_push($a_sks, $o_score->curriculum_subject_credit);
                            array_push($a_score_id, $o_score->score_id);
                            $i_count_subject++;
                            // $s_subject_list.= "{$i_count_subject}. {$o_score->subject_name} (Rp {$d_subject_fee}) \n";
                        }
                        $i_sum_sks = array_sum($a_sks);

                        $mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($o_student->personal_data_id, $mbo_fee_data->fee_id);
                        if (!$mbo_student_invoice_data) {
                            print('err:'.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                            exit;
                        }else{
                            $invoice_full = $this->Im->get_invoice_full_payment($mbo_student_invoice_data->invoice_id);

                            if (!$invoice_full) {
                                print('no full payment created:'.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                                print('exit');exit;
                            }else{
                                if ($invoice_full->sub_invoice_details_amount_paid == 0) {
                                    // $s_description = "Full Payment {$mbo_fee_data->fee_description} Batch {$o_student->finance_year_id} for {$i_sum_sks} credits";

                                    // $this->Im->update_sub_invoice_details(['sub_invoice_details_description' => $s_description], ['sub_invoice_details_id' => $invoice_full->sub_invoice_details_id]);
                                    // print json_encode($mbo_student_invoice_data->invoice_id);
                                    // print('<br>');

                                    $mbo_student_family = $this->Fmm->get_family_by_personal_data_id($o_student->personal_data_id);
                                    $s_email_to = $o_student->student_email;
                                    $a_email_cc = array('', '');
                                    $s_email_subject = "Confirmation Payment of Subject Registration for Short Semester";

                                    if ($mbo_student_family) {
                                        $mba_parent_data = $this->Fmm->get_family_lists_filtered(array(
                                            'fmm.family_id' => $mbo_student_family->family_id,
                                            'fmm.family_member_status != ' => 'child'
                                        ));
                            
                                        if ($mba_parent_data) {
                                            foreach ($mba_parent_data as $o_parents) {
                                                if (!in_array($o_parents->personal_data_email, $a_email_cc)) {
                                                    array_push($a_email_cc, $o_parents->personal_data_email);
                                                }
                                            }
                                        }
                                    }

                                    $a_template_param = array(
                                        'invoice_id' => $mbo_student_invoice_data->invoice_id,
                                        'subjects_count' => $i_count_subject,
                                        'transfer_amount' => $invoice_full->sub_invoice_details_amount,
                                        'va_number' => $invoice_full->sub_invoice_details_va_number,
                                        'personal_data_name' => $o_student->personal_data_name,
                                        'a_score_id' => $a_score_id,
                                        'payment_deadline' => date('d F Y', strtotime($invoice_full->sub_invoice_details_deadline))
                                    );

                                    $s_body_message = modules::run('messaging/text_template/short_semester_billing', $a_template_param);
                                    // $config = $this->config->item('mail_config');
                                    // $config['mailtype'] = 'html';
                                    // $this->email->initialize($config);

                                    $this->email->from('employee@company.ac.id', 'IULI Academic Services Centre');
                                    $this->email->cc($a_email_cc);
                                    $this->email->subject($s_email_subject);
                                    $this->email->message($s_body_message);

                                    if(!$this->email->send()){
                                        $this->log_activity('Email did not sent');
                                        $this->log_activity('Error Message: '.$this->email->print_debugger());
                                        
                                        $a_return = array('code' => 1, 'message' => 'Email not send to '.$s_email_to.' !');
                                        // print json_encode($a_return);exit;
                                    }else{
                                        $a_return = array('code' => 0, 'message' => 'Success send to:'.$o_student->personal_data_name);
                                    }

                                    // print($o_student->personal_data_name);
                                    print json_encode($a_return);
                                    print('<br>');
                                }
                            }
                        }
                    }

                    // print($o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                    // print('<br>');
                    $i_count++;
                }else{
                    print('Fee '.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number.' ga ada!<br>');
                }
            }

            print('<h1>'.$i_count.'</h1>');
        }
    }

    function get_assessment_responden() {
        $this->load->model('validation_requirement/Assessment_model', 'Asm');
        $mba_assessment_result = $this->Asm->get_result([
            'qr.assessment_id' => '6300dd69-b415-11ed-9d77-52540039e1c3',
            'st.study_program_id' => '7da8cd1e-8f0e-41f4-89dd-361c29801087'
        ], true);
        if ($mba_assessment_result) {
            print('<table border="1">');
            foreach ($mba_assessment_result as $o_result) {
                print('<tr>');
                print('<td>'.$o_result->personal_data_name.'</td>');
                print('<td>'.$o_result->student_number.'</td>');
                print('<td>'.$o_result->student_batch.'</td>');
                print('<td>'.$o_result->study_program_abbreviation.'</td>');
                // print('<td>'.$s_advisor4.'</td>');
                print('</tr>');
            }
            print('</table>');
        }
        print('<pre>');var_dump($mba_assessment_result);exit;
    }

    function test_va_candidate() {
        $s_study_program_id = '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89';
        $mba_student_filtered = $this->Bnim->get_va_enrollment_sequence('01', '24');
        print('<pre>');var_dump($mba_student_filtered);
        // $s_va = '8310102302001001';
        // $s_sequence = substr($s_va, 10, -3);
        // print('<pre>');var_dump($s_sequence);
    }

    public function get_advisor_student_list() {
        $a_student_number = ['11201907012','11201907004','11201907014','11201907013','11201607008','11201807010','11201909002','11201909007','11201809002','11201910007','11201910005','11201910001','11202008011','11201808006','11201908005','11201908003','11201908002','11201808001','11201808009','11201608004','11201906002','11201806003','11201906003','11201905001','11201905002','11201902001','11201902012','11201902007','11201902005','11201902013','11201602025','11201803011','11201703008','11201813004','11201813002','11201901018','11201901002','11202001004','11201801002','11201601024','11201901006','11201904003','11201904006','11201604004','11201912009','11201912011','11201912005','11201907001','11201907003','11201907008','11201907002','11201907015','11201806005','11201806002','11201902008','11201901004','11201901020','11201902014','11201909003'];
        print('<table border="1">');
        foreach ($a_student_number as $s_student_number) {
            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number]);
            if (!$mba_student_data) {
                print($s_student_number.' ga ada!!!');exit;
            }
            $o_student = $mba_student_data[0];
            $mba_thesis_students_ata = $this->General->get_in('thesis_students', 'current_progress', ['finish', 'final', 'work'], ['student_id' => $o_student->student_id]);
            if ($mba_thesis_students_ata) {
                $o_thesis_student = $mba_thesis_students_ata[0];
                $mba_thesis_advisor_approved_1 = $this->Tsm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_student->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_1'
                ], 'advisor');
                $mba_thesis_advisor_approved_2 = $this->Tsm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_student->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_2'
                ], 'advisor');
                $mba_thesis_advisor_approved_3 = $this->Tsm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_student->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_3'
                ], 'advisor');
                $mba_thesis_advisor_approved_4 = $this->Tsm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_student->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_4'
                ], 'advisor');

                $s_advisor1 = ($mba_thesis_advisor_approved_1) ? $this->General->retrieve_title($mba_thesis_advisor_approved_1[0]->personal_data_id) : '';
                $s_advisor2 = ($mba_thesis_advisor_approved_2) ? $this->General->retrieve_title($mba_thesis_advisor_approved_2[0]->personal_data_id) : '';
                $s_advisor3 = ($mba_thesis_advisor_approved_3) ? $this->General->retrieve_title($mba_thesis_advisor_approved_3[0]->personal_data_id) : '';
                $s_advisor4 = ($mba_thesis_advisor_approved_4) ? $this->General->retrieve_title($mba_thesis_advisor_approved_4[0]->personal_data_id) : '';
                // print('<pre>');var_dump($mba_thesis_advisor_approved_1);exit;

                print('<tr>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td>'.$s_advisor1.'</td>');
                print('<td>'.$s_advisor2.'</td>');
                print('<td>'.$s_advisor3.'</td>');
                print('<td>'.$s_advisor4.'</td>');
                print('</tr>');
            }
            else {
                print('<tr>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td></td>');
                print('<td></td>');
                print('<td></td>');
                print('<td></td>');
                print('</tr>');
            }
        }
        print('</table>');
    }

    public function get_advisor_list()
    {
        $s_template_path = APPPATH.'uploads/temp/student_graduation_advisor.xlsx';
        $s_file_name = 'student_graduation_advisor';
        $s_filename = $s_file_name.'.xlsx';

        $s_file_path = APPPATH."uploads/temp/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();

        $i_count = 0;
        $i_row = 2;
        $a_advisor = [];
        // $this->db->trans_begin();
        while ($o_sheet->getCell('A'.$i_row)->getValue() !== NULL) {
            $s_student_name = $o_sheet->getCell("A$i_row")->getValue();
            $s_student_number = $o_sheet->getCell("B$i_row")->getValue();
            $s_thesis_title = $o_sheet->getCell("F$i_row")->getValue();

            // $s_advisor_1 = trim(strtolower($o_sheet->getCell("H$i_row")->getValue()));
            // $s_advisor_2 = trim(strtolower($o_sheet->getCell("J$i_row")->getValue()));
            // $s_advisor_3 = trim(strtolower($o_sheet->getCell("L$i_row")->getValue()));

            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_name' => $s_student_name,
                'ds.student_number' => $s_student_number,
                'ds.student_status' => 'graduated'
            ]);
            $o_student_data = $mba_student_data[0];
            $mba_thesis_student_data = $this->General->get_where('thesis_students', ['student_id' => $o_student_data->student_id]);
            $mbaold_thesis_student_data = $this->General->get_where('thesis_student', ['student_id' => $o_student_data->student_id]);
            if (!$mba_thesis_student_data) {
                print($s_student_number.' thesis not found!');exit;
            }
            $o_thesis_student_data = $mba_thesis_student_data[0];

            // if ($mbaold_thesis_student_data) {
            //     $o_old_thesis_student_data =$mbaold_thesis_student_data[0];
            //     $mba_thesis_examiner = $this->General->get_where('thesis_student_examiner', ['thesis_student_id' => $o_old_thesis_student_data->thesis_student_id]);
            //     if ($mba_thesis_examiner) {
            //         foreach ($mba_thesis_examiner as $o_examiner) {
            //             $a_thesis_examiner_data = [
            //                 'student_examiner_id' => $o_examiner->student_examiner_id,
            //                 'thesis_student_id' => $o_thesis_student_data->thesis_student_id,
            //                 'advisor_id' => $o_examiner->advisor_id,
            //                 'examiner_type' => $o_examiner->examiner_type,
            //                 'date_added' => $o_examiner->date_added
            //             ];

            //             $this->db->insert('thesis_students_examiner', $a_thesis_examiner_data);
            //         }
            //     }
            // }

            $i_row++;
        }
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            print('<h1>Rollback</h1>');
        }
        else {
            $this->db->trans_commit();
            print('<h1>Commit</h1>');
        }
    }

    // public function get_bad_score()
    // {
    //     $fc_eng_id = '301a3e19-348d-4398-b640-c9d2acc491fa';
    //     $mba_score_list = $this->Scm->get_score_data([
    //         'sc.score_approval' => 'approved',
    //         'sc.score_sum < ' => 55.5,
    //         'curs.curriculum_subject_type != ' => 'extracurricular',
    //         'curs.regular semester' => 'regular semester',
    //         'curs.curriculum_subject_credit >' => '0'
    //     ]);

    //     if ($mba_score_list) {
    //         $s_file_name = str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));
    //         $s_folder_master = $s_file_name.'_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$s_class_master_id;

    //         $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

    //         if(!file_exists($path_master)){
    //             mkdir($path_master, 0777, TRUE);
    //         }

    //         $s_file = 'Student_score_'.$s_file_name.'_'.$mbo_class_master_data->running_year.'_'.$mbo_class_master_data->class_semester_type_id.'_'.$mbo_class_master_data->class_master_id;

    //         $s_file_path = $path_master.$s_file.".csv";
    //         $fp = fopen($s_file_path, 'w+');
            
    //         fputcsv($fp, [
    //             'Student Name', 'Student ID', 'Study Program', 'Subject Name', 'Credit/SKS', 'Quiz', 'Final Exam', 'Repetition Exam', 'Final Score', 'Grade', 'Grade Point'
    //         ]);
    //         foreach ($mba_score_list as $o_score) {
    //             $mbo_student_data = $this->Stm->get_student_filtered([
    //                 'ds.student_id' => $o_score->student_id
    //             ])[0];

    //             if (($mbo_student_data) AND $mbo_student_data->faculty_id == $fc_eng_id) {
    //                 // get_good_grades($s_subject_name, $s_student_id, $d_score_sum = null)
    //                 $s_score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
    //                 $s_grade_point = $this->grades->get_grade_point($s_score_sum);
    //                 $s_grade = $this->grades->get_grade($s_score_sum);

    //                 if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
    //                     fputcsv($fp, [
    //                         $mbo_student_data->personal_data_name,
    //                         $mbo_student_data->student_number,
    //                         $mbo_student_data->study_program_abbreviation,
    //                         $o_score->subject_name,
    //                         $o_score->curriculum_subject_credit,
    //                         $o_score->score_quiz,
    //                         $o_score->score_final_exam,
    //                         $o_score->score_repetition_exam,
    //                         $s_score_sum,
    //                         $s_grade,
    //                         $s_grade_point,
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    // }

    public function get_peserta_kelas()
    {
        $s_query = "SELECT 
            CONCAT(sp.study_program_abbreviation, sm.semester_number) AS 'class_name',
            sp.study_program_id,
            sc.semester_id,
            sp.study_program_name,
            sm.semester_number
            FROM dt_score sc
            JOIN dt_class_group cg ON cg.class_group_id = sc.class_group_id
            JOIN dt_class_group_subject cgs ON cgs.class_group_id = cg.class_group_id
            JOIN dt_offered_subject ofs ON ofs.offered_subject_id = cgs.offered_subject_id
            JOIN ref_study_program sp ON sp.study_program_id = ofs.study_program_id
            JOIN ref_curriculum_subject cs ON cs.curriculum_subject_id = ofs.curriculum_subject_id
            JOIN ref_semester sm ON sm.semester_id = sc.semester_id
            WHERE sc.academic_year_id = 2020
            AND sc.semester_type_id = 2
            AND sc.score_approval = 'approved'
            GROUP BY sp.study_program_abbreviation, sm.semester_number";

        $query = $this->db->query($s_query);
        $mba_kelas = ($query->num_rows() > 0) ? $query->result() : false;
        if ($mba_kelas) {
            print('<table border="1">');
            foreach ($mba_kelas as $o_kelas) {
                $s_query = "SELECT *
                FROM dt_score sc
                JOIN dt_student st ON st.student_id = sc.student_id
                WHERE sc.academic_year_id = 2020
                AND sc.semester_type_id = 2
                AND sc.score_approval = 'approved'
                AND st.study_program_id = '$o_kelas->study_program_id'
                AND sc.semester_id = '$o_kelas->semester_id'";

                $query_total_siswa = $this->db->query($s_query);
                $o_kelas->total_siswa = $query_total_siswa->num_rows();
                print('<tr>');
                print('<td>'.$o_kelas->class_name.'</td>');
                print('<td>'.$o_kelas->study_program_name.'</td>');
                print('<td>'.$o_kelas->semester_number.'</td>');
                print('<td>'.$query_total_siswa->num_rows().'</td>');
                print('</tr>');
            }
            print('</table>');
        }
        // var_dump($query->result());
    }

    public function student_with_nfu_invoice()
    {
        $mba_student_nfu_invoice = $this->General->get_join('dt_student st', array(
            'dt_personal_data pd' => 'pd.personal_data_id = st.personal_data_id',
            'dt_invoice inv' => 'inv.personal_data_id = pd.personal_data_id',
            'dt_invoice_details idd' => 'idd.invoice_id = inv.invoice_id'
        ), array(
            'idd.fee_id' => 'bb6c6d9b-dc03-4848-ac88-d704541f0300'
        ));

        if ($mba_student_nfu_invoice) {
            print('<pre>');
            var_dump($mba_student_nfu_invoice);exit;
        }
    }

    // public function generate_tuition_fee_report(
    //     $s_batch,
    //     $s_finance_year_id = 'all',
    //     $s_program_id = 'all',
    //     $s_study_program_id = 'all',
    //     $a_student_status = ['active', 'inactive', 'onleave']
    // )
	// {
	// 	$a_filter_data = [
    //         'ds.academic_year_id' => $s_academic_year_id,
    //         'ds.finance_year_id' => $s_finance_year_id,
    //         'ds.program_id' => $s_program_id,
    //         'ds.study_program_id' => $s_study_program_id
    //     ];

    //     foreach ($a_filter_data as $key => $value) {
    //         if ($value == 'all') {
    //             unset($a_filter_data[$key]);
    //         }
    //     }
	// }

    public function generate_report()
    {
        $this->generate_invoice_with_installment_report_by_batch(
            'all',
            'all',
            '1', 
            'all',
            ['active', 'inactive', 'onleave', 'resign'],
            '02'
        );
    }

    public function report_for_nfu()
    {
        $this->generate_invoice_with_installment_report_by_batch(
            'all',
            'all',
            '1', 
            'all',
            ['active', 'inactive', 'onleave', 'resign'],
            '02',
            'nfu'
        );
    }

    public function invoice_report_student_srh()
    {
        $this->load->model('partner/Partner_student_model', 'Psm');
        $mba_partner_period = $this->Psm->get_partner_period('b4b57aa6-90b6-11eb-9a90-52540001273f');
        // print('<pre>');var_dump($mba_partner_period);exit;
        
        if ($mba_partner_period) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'SHR_Invoice_Report_'.date('Y-m-d');
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/custom_report/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $style_vertical_center = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                )
            );

            $o_spreadsheet = IOFactory::load("$s_template_path");
            $i_sheet = 0;
            foreach ($mba_partner_period as $o_period) {
                $mba_student_partner_data = $this->Psm->get_partner_student_data([
                    'sn.partner_period_id' => $o_period->partner_period_id
                ]);

                if ($mba_student_partner_data) {
                    $sheet_name = $o_period->academic_year_id.'_'.$o_period->partner_period;
                    if ($i_sheet > 0) {
                        $o_spreadsheet->createSheet();
                    }
                    
                    $o_sheet = $o_spreadsheet->getSheet($i_sheet)->setTitle($sheet_name);
                    $i_sheet++;

                    $o_sheet->setCellValue('A1', 'Name');
                    $o_sheet->setCellValue('B1', 'Phone Number');
                    $o_sheet->setCellValue('C1', 'Email');
                    $o_sheet->setCellValue('D1', 'Billed Amount');
                    $o_sheet->setCellValue('E1', 'Installment Paid');

                    $o_sheet->mergeCells('A1:A2');
                    $o_sheet->mergeCells('B1:B2');
                    $o_sheet->mergeCells('C1:C2');
                    $o_sheet->mergeCells('D1:D2');
                    $o_sheet->getStyle('A1:D2')->applyFromArray($style_vertical_center);

                    $i_start_row = $i_row = 3;
                    $i_total_installment = 0;
                    $c_cols_installment_start = $c_cols_installment_end = 'E';

                    foreach ($mba_student_partner_data as $o_student_partner) {
                        $mba_student_partner_invoice_details = $this->Im->student_has_invoice_data($o_student_partner->personal_data_id);
                        if ($mba_student_partner_invoice_details->invoice_status == 'cancelled') {
                            $o_sheet->getStyle('A'.$i_row.':D'.$i_row)->getFill()
                                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('FF0101');
                        }

                        $o_sheet->setCellValue('A'.$i_row, $o_student_partner->personal_data_name);
                        $o_sheet->setCellValue('B'.$i_row, '="'.$o_student_partner->personal_data_cellular.'"');
                        $o_sheet->setCellValue('C'.$i_row, $o_student_partner->personal_data_email);
                        
                        if ($mba_student_partner_invoice_details) {
                            $o_invoice_data = $mba_student_partner_invoice_details;
                            $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice_data->invoice_id);
                            $i_count_installment = ($mba_invoice_installment) ? count($mba_invoice_installment) : 0;

                            $o_sheet->setCellValue('D'.$i_row, $o_invoice_data->invoice_details_amount);
                            $o_sheet->getStyle('D'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                            
                            if ($mba_invoice_installment) {
                                $c_installment = $c_cols_installment_start;
                                foreach ($mba_invoice_installment as $o_installment) {
                                    $o_sheet->setCellValue($c_installment.$i_row, $o_installment->sub_invoice_details_amount_paid);
                                    $o_sheet->getStyle($c_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                                    $c_installment++;
                                }

                                if ($i_total_installment < count($mba_invoice_installment)) {
                                    $i_total_installment = count($mba_invoice_installment);
                                    $c_cols_installment_end = $c_installment;
                                }
                            }
                        }

                        $i_row++;
                    }

                    $i_installment_number = 1;
                    $c_installment_header = $c_cols_installment_start;
                    for ($i=1; $i <= $i_total_installment; $i++) { 
                        $o_sheet->setCellValue($c_installment_header.'2', $i);
                        if ($i < $i_total_installment) {
                            $c_installment_header++;
                        }
                    }
                    
                    $o_sheet->mergeCells($c_cols_installment_start.'1:'.$c_installment_header.'1');

                    $c_col = 'A';
                    for ($i = 1; $i < 5; $i++) { 
                        $o_sheet->getColumnDimension($c_col++)->setAutoSize(true);
                    }

                    $c_max_installment = $c_installment_header;
                    $c_header_tpaid = ++$c_max_installment;
                    $c_header_touts = ++$c_max_installment;
                    // print($c_header_touts);exit;
                    // $c_header_touts = $c_header_tpaid + 1;
                    $i_row = $i_start_row;
                    $o_sheet->setCellValue($c_header_tpaid.'1', 'Total Paid');
                    $o_sheet->mergeCells($c_header_tpaid.'1:'.$c_header_tpaid.'2');
                    $o_sheet->setCellValue($c_header_touts.'1', 'Outstanding');
                    $o_sheet->mergeCells($c_header_touts.'1:'.$c_header_touts.'2');
                    $o_sheet->getStyle($c_header_tpaid.'1:'.$c_header_touts.'1')->applyFromArray($style_vertical_center);

                    foreach ($mba_student_partner_data as $o_student_partner) {
                        $o_sheet->setCellValue($c_header_tpaid.$i_row, '=SUM('.$c_cols_installment_start.$i_row.':'.$c_installment_header.$i_row.')');
                        $o_sheet->setCellValue($c_header_touts.$i_row, '=D'.$i_row.'-'.$c_header_tpaid.$i_row);
                        $o_sheet->getStyle($c_header_tpaid.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        $o_sheet->getStyle($c_header_touts.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        $i_row++;
                    }
                    $o_sheet->getColumnDimension($c_header_tpaid)->setAutoSize(true);
                    $o_sheet->getColumnDimension($c_header_touts)->setAutoSize(true);
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    public function fill_data_a()
    {
        $s_template_path = APPPATH.'uploads/temp/student_body_Engineering.xlsx';
        $s_file_name = 'student_body_Engineering_Profile';
        $s_filename = $s_file_name.'.xlsx';

        $s_file_path = APPPATH."uploads/temp/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $a_sheet_index = [1,2,3,4,5];
        $o_spreadsheet = IOFactory::load("$s_template_path");

        foreach ($a_sheet_index as $i_sheet_index) {
            $o_sheet = $o_spreadsheet->getSheet($i_sheet_index);
            $i_row = 3;

            while ($o_sheet->getCell('B'.$i_row)->getValue() !== NULL) {
                $s_student_number = $o_sheet->getCell("B$i_row")->getValue();
                $s_student_name = $o_sheet->getCell("A$i_row")->getValue();

                $mbo_student_data = $this->Stm->get_student_filtered([
                    'ds.student_number' => $s_student_number,
                    'dpd.personal_data_name' => $s_student_name
                ]);

                if (!$mbo_student_data) {
                    print('row number '.$i_row.' sheet '.$i_sheet_index.' not found in database!');exit;
                }

                $o_student = $mbo_student_data[0];
                $mba_country = false;
                if (!is_null($o_student->address_country_id)) {
                    $mba_country = $this->General->get_where('ref_country', ['country_id' => $o_student->address_country_id]);
                }

                $birthdate = new DateTime($o_student->personal_data_date_of_birth);
                $today = new DateTime("today");
                $i_birthday = $today->diff($birthdate)->y;
                $s_gender = ($o_student->personal_data_gender == 'M') ? "Laki Laki" : (($o_student->personal_data_gender == 'F') ? "Perempuan" : "?");
                $s_place_of_birth = $o_student->personal_data_place_of_birth;
                $s_place_of_birth = (($s_place_of_birth == '') OR (is_null($s_place_of_birth))) ? '' : $s_place_of_birth.', ';
                $s_date_of_birth = date('d F Y', strtotime($o_student->personal_data_date_of_birth));
                // print('<pre>');var_dump($o_student->personal_data_date_of_birth);exit;
                $city = $o_student->address_city;
                $s_county = ($mba_country) ? $mba_country[0]->country_name : '';

                $o_sheet->setCellValue('D'.$i_row, $s_gender);
                $o_sheet->setCellValue('E'.$i_row, $s_place_of_birth.$s_date_of_birth);
                $o_sheet->setCellValue('F'.$i_row, $i_birthday);
                $o_sheet->setCellValue('G'.$i_row, $city);
                $o_sheet->setCellValue('H'.$i_row, $s_county);
                $i_row++;
            }
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_filename);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        readfile( $s_file_path.$s_filename );
        exit;
    }

    function get_sekolahan_dapo()
    {
        $s_id_semester = '20232';
        $this->load->library('LibAPI');
        // provinsi
        $url_wilayah_sma = 'https://dapo.kemdikbud.go.id/rekap/progres-sma?id_level_wilayah=0&kode_wilayah=000000&semester_id='.$s_id_semester;
        $wilayah_sma = $this->libapi->get_data($url_wilayah_sma);
        if ($wilayah_sma) {
            foreach ($wilayah_sma as $o_wilayah) {
                // Kabupaten
                $url_sub_wil_sma = 'https://dapo.kemdikbud.go.id/rekap/progres-sma?id_level_wilayah='.$o_wilayah->id_level_wilayah.'&kode_wilayah='.trim($o_wilayah->kode_wilayah).'&semester_id='.$s_id_semester;
                $sub_wilayah_sma = $this->libapi->get_data($url_sub_wil_sma);
                // print("<pre>");var_dump($sub_wilayah_sma);exit;
                if ($sub_wilayah_sma) {
                    foreach ($sub_wilayah_sma as $o_sub_wilayah) {
                        // Kecamatan
                        $url_kec_wil_sma = 'https://dapo.kemdikbud.go.id/rekap/progres-sma?id_level_wilayah='.$o_sub_wilayah->id_level_wilayah.'&kode_wilayah='.trim($o_sub_wilayah->kode_wilayah).'&semester_id='.$s_id_semester;
                        $kec_wilayah_sma = $this->libapi->get_data($url_kec_wil_sma);
                        // print("<pre>");var_dump($kec_wilayah_sma);
                        // exit;
                        if ($kec_wilayah_sma) {
                            foreach ($kec_wilayah_sma as $o_kec_wilayah) {
                                // Kecamatan
                                $url_sma = 'https://dapo.kemdikbud.go.id/rekap/progresSP-sma?id_level_wilayah='.$o_kec_wilayah->id_level_wilayah.'&kode_wilayah='.trim($o_kec_wilayah->kode_wilayah).'&semester_id='.$s_id_semester;
                                $data_sma = $this->libapi->get_data($url_sma);
                                print("<pre>");var_dump($data_sma);exit;
                            }
                        }
                    }
                }
            }
        }
        // print("<pre>");var_dump($get_data);exit;
	
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url_first);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// // curl_setopt($ch, CURLOPT_HEADER, false);
		// curl_setopt($ch, CURLOPT_VERBOSE, false);
		// // curl_setopt($ch, CURLOPT_NOBODY, true);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// curl_setopt($ch, CURLOPT_ENCODING, true);
		// curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		// // curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	
		// // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");
	
		// $rs = curl_exec($ch);
		// curl_close($ch);
        // print("<pre>");var_dump($rs);exit;
    }

    public function fill_historycall()
    {
        $s_template_path = APPPATH.'uploads/temp/template_student_history.xlsx';
        $s_file_name = 'template_student_history';
        $s_filename = $s_file_name.'.xlsx';

        $s_file_path = APPPATH."uploads/temp/result/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $a_student_number_processed = [];
        $i_sheet = 1;
        $i_row = 2;
        while ($o_sheet->getCell('C'.$i_row)->getValue() !== NULL) {
            $s_student_number = str_replace('="', '', trim($o_sheet->getCell("C$i_row")->getValue()));
            $s_student_number = str_replace('"', '', $s_student_number);
            $s_student_number = str_replace('?', '', $s_student_number);
            // print('<pre>');var_dump($s_student_number);exit;
            
            $personal_name = str_replace('="', '', trim($o_sheet->getCell("A$i_row")->getValue()));
            $personal_name = str_replace('"', '', $personal_name);
            if ((!in_array($s_student_number, $a_student_number_processed)) AND ($s_student_number != '')) {
                array_push($a_student_number_processed, $s_student_number);

                $mbo_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number]);
                $o_student_data = $this->General->get_where('dt_student', ['student_number' => $s_student_number]);
                if (!$mbo_student_data) {
                    print('student line '.$i_row.' not found!');exit;
                }
                $mbo_student_data = $mbo_student_data[0];
                $o_student_data = $o_student_data[0];
                $sheet_name = $mbo_student_data->personal_data_name;
                if (strlen($sheet_name) > 30) {
                    $a_names = explode(' ', $sheet_name);
                    $sheet_name = $a_names[0].' '.$a_names[1];
                }

                $o_spreadsheet->createSheet();
                $o_sheet_history = $o_spreadsheet->getSheet($i_sheet)->setTitle($sheet_name);
                $i_sheet++;

                $a_bni_id = [];
                $a_data = [];

                $bni_transaction = false;
                if ($bni_transaction) {
                    foreach ($bni_transaction as $o_mdb_bni) {
                        if (!in_array($o_mdb_bni->id, $a_bni_id)) {
                            array_push($a_bni_id, $o_mdb_bni->id);
                        }
                    }
                }

                if ((!is_null($o_student_data->portal_id)) AND ($o_student_data->portal_id != 0)) {
                    $mba_portal_invoice_student = false;
                    if ($mba_portal_invoice_student) {
                        foreach ($mba_portal_invoice_student as $o_invoice) {
                            $mba_invoice_sub = false;
                            if ($mba_invoice_sub) {
                                foreach ($mba_invoice_sub as $o_invoice_sub) {
                                    $mba_invoice_sub_details = false;
                                    if ($mba_invoice_sub_details) {
                                        foreach ($mba_invoice_sub_details as $o_invoice_sub_details) {
                                            $s_payment_code = substr($o_invoice_sub_details->virtual_account, 4, 2);
                                            $mbo_payment_type = false;
                                            $o_bni_transaction = false;
                                            $paid_amount = (is_null($o_invoice_sub_details->amount_paid)) ? ((($o_bni_transaction) ? $o_bni_transaction->payment_amount : '')) : $o_invoice_sub_details->amount_paid;
    
                                            array_push($a_data, [
                                                'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->name : '',
                                                'billed_amount' => $o_invoice_sub_details->amount,
                                                'description' => $o_invoice_sub_details->description,
                                                'paid_amount' => (($o_bni_transaction) AND ($o_bni_transaction->payment_amount > $paid_amount)) ? $o_bni_transaction->payment_amount : $paid_amount,
                                                'note_invoice' => $o_invoice->notes,
                                                'status' => $o_invoice_sub_details->status,
                                                'note_va' => $o_invoice_sub_details->additional_description.' - '.$o_invoice_sub_details->change_details,
                                                'datetime_payment' => ($o_bni_transaction) ? $o_bni_transaction->datetime_payment : '',
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
    
                $mba_staging_invoice_data = $this->General->get_where('dt_invoice', ['personal_data_id' => $mbo_student_data->personal_data_id]);
                if ($mba_staging_invoice_data) {
                    foreach ($mba_staging_invoice_data as $o_invoice) {
                        $mba_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $o_invoice->invoice_id]);
                        if ($mba_sub_invoice) {
                            foreach ($mba_sub_invoice as $o_sub_invoice) {
                                $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', ['sub_invoice_id' => $o_sub_invoice->sub_invoice_id]);
                                if ($mba_sub_invoice_details) {
                                    foreach ($mba_sub_invoice_details as $o_invoice_sub_details) {
                                        $s_payment_code = substr($o_invoice_sub_details->sub_invoice_details_va_number, 4, 2);
                                        $mbo_payment_type = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_payment_code])[0];
                                        array_push($a_data, [
                                            'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->payment_type_name : '',
                                            'billed_amount' => $o_invoice_sub_details->sub_invoice_details_amount,
                                            'description' => $o_invoice_sub_details->sub_invoice_details_description,
                                            'paid_amount' => $o_invoice_sub_details->sub_invoice_details_amount_paid,
                                            'note_invoice' => $o_invoice->invoice_note,
                                            'status' => $o_invoice->invoice_status,
                                            'note_va' => (!is_null($o_invoice_sub_details->sub_invoice_details_remarks)) ? $o_invoice_sub_details->sub_invoice_details_remarks : '',
                                            'datetime_payment' => $o_invoice_sub_details->sub_invoice_details_datetime_paid_off,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
    
                if (count($a_data) > 0) {
                    $a_data = array_values($a_data);
    
                    $s_student_name = str_replace(' ', '_', strtolower($mbo_student_data->personal_data_name));
                    $i_row_sheet = 1;
                    $o_sheet_history->setCellValue('A'.$i_row_sheet, 'Payment Type');
                    $o_sheet_history->setCellValue('B'.$i_row_sheet, 'Billed Amount');
                    $o_sheet_history->setCellValue('C'.$i_row_sheet, 'Description');
                    $o_sheet_history->setCellValue('D'.$i_row_sheet, 'Paid Amount');
                    $o_sheet_history->setCellValue('E'.$i_row_sheet, 'Datetime Payment');
                    $o_sheet_history->setCellValue('F'.$i_row_sheet, 'Billing Status');
                    $o_sheet_history->setCellValue('G'.$i_row_sheet, 'Invoice Note');
                    $o_sheet_history->setCellValue('H'.$i_row_sheet, 'Billing Note');
                    $i_row_sheet++;
    
                    foreach ($a_data as $data) {
                        $o_sheet_history->setCellValue('A'.$i_row_sheet, $data['payment_type']);
                        $o_sheet_history->setCellValue('B'.$i_row_sheet, $data['billed_amount']);
                        $o_sheet_history->setCellValue('C'.$i_row_sheet, $data['description']);
                        $o_sheet_history->setCellValue('D'.$i_row_sheet, $data['paid_amount']);
                        $o_sheet_history->setCellValue('E'.$i_row_sheet, $data['datetime_payment']);
                        $o_sheet_history->setCellValue('F'.$i_row_sheet, $data['status']);
                        $o_sheet_history->setCellValue('G'.$i_row_sheet, $data['note_invoice']);
                        $o_sheet_history->setCellValue('H'.$i_row_sheet, $data['note_va']);
                        $i_row_sheet++;
                    }
                    
                }
            }
            $i_row++;
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_filename);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        readfile( $s_file_path.$s_filename );
        exit;
    }

    function extend_va_expired() {
        exit;
        $s_new_date = '2025-01-10 23:59:59';
        $s_template_path = APPPATH.'uploads/temp/New VA Active.xlsx';

        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        // $o_spreadsheet->getProperties()
        //     ->setTitle($)
        //     ->setCreator("IULI Finance Services")
        //     ->setCategory("Invoice Student Report");

        $i_row = 2;
        print('<pre>');
        while ($o_sheet->getCell('A'.$i_row)->getValue() !== NULL) {
            $s_va_number = trim(str_replace('"', '', str_replace('="', '', $o_sheet->getCell('A'.$i_row)->getValue())));
            $s_name = trim(str_replace('"', '', str_replace('="', '', $o_sheet->getCell('B'.$i_row)->getValue())));
            $s_trx_id = trim(str_replace('"', '', str_replace('="', '', $o_sheet->getCell('C'.$i_row)->getValue())));
            $s_amount = trim(str_replace('"', '', str_replace('="', '', $o_sheet->getCell('J'.$i_row)->getValue())));
            $s_description = trim(str_replace('"', '', str_replace('="', '', $o_sheet->getCell('L'.$i_row)->getValue())));
            $s_expired = trim(str_replace('"', '', str_replace('="', '', $o_sheet->getCell('G'.$i_row)->getValue())));

            if ($s_new_date != $s_expired) {
                $s_payment_code = substr($s_va_number, -2);
                $a_update_billing = array(
                    'trx_id' => $s_trx_id,
                    'trx_amount' => $s_amount,
                    'customer_name' => $s_name,
                    'datetime_expired' => $s_new_date,
                    'description' => ($s_payment_code != '02') ? 'Billing '.$s_name : $s_description,
                );
                $update = $this->Bnim->update_billing($a_update_billing);
                var_dump($update);
                // var_dump($a_update_billing);
                // exit;
                print('<br>');
            }

            $i_row++;
        }
    }

    public function copy_ofse_offered_subject() {
        print('function closed!');exit;
        // $this->load->model('academic/Ofse_model', 'Osm');
        // $s_old_ofse_period_id = '0245741d-3e98-4cfb-bd1e-1f350b34d10d';
        // $s_new_ofse_period_id = '26e6b83c-6752-4d0f-905e-0fd121e9a4a7';
        // $s_for_academic_year_id = 2023;
        // $s_for_semester_type_id = 6;

        // $mba_ofse_data = $this->Osm->get_ofse_class(['ofs.ofse_period_id' => $s_old_ofse_period_id]);

        // $this->db->trans_begin();
        // foreach ($mba_ofse_data as $o_ofse_data) {
        //     $s_class_group_id = $this->uuid->v4();
        //     $s_offered_subject_id = $this->uuid->v4();
        //     $a_class_group_data = [
        //         'class_group_id' => $s_class_group_id,
        //         'academic_year_id' => $s_for_academic_year_id,
        //         'semester_type_id' => $s_for_semester_type_id,
        //         'class_group_name' => $o_ofse_data->class_group_name
        //     ];

        //     $a_offered_subject_data = [
        //         'offered_subject_id' => $s_offered_subject_id,
        //         'curriculum_subject_id' => $o_ofse_data->curriculum_subject_id,
        //         'academic_year_id' => $s_for_academic_year_id,
        //         'semester_type_id' => $s_for_semester_type_id,
        //         'program_id' => $o_ofse_data->program_id,
        //         'study_program_id' => $o_ofse_data->study_program_id,
        //         'ofse_period_id' => $s_new_ofse_period_id,
        //         'ofse_status' => $o_ofse_data->ofse_status
        //     ];

        //     $a_class_group_subject_data = [
        //         'class_group_subject_id' => $this->uuid->v4(),
        //         'class_group_id' => $s_class_group_id,
        //         'offered_subject_id' => $s_offered_subject_id
        //     ];

        //     $this->db->insert('dt_class_group', $a_class_group_data);
        //     $this->db->insert('dt_offered_subject', $a_offered_subject_data);
        //     $this->db->insert('dt_class_group_subject', $a_class_group_subject_data);

        //     print('processing '.$o_ofse_data->subject_name.' '.$o_ofse_data->curriculum_subject_code);
        //     print('<br>');
        // }

        // if ($this->db->trans_status() === FALSE) {
        //     $this->db->trans_rollback();
        //     print('rollback');
        // }
        // else {
        //     $this->db->trans_commit();
        //     print('commit');
        // }


        // print('<pre>');var_dump('finish');exit;
    }

    public function get_historycal_payment($s_student_id)
    {
        error_reporting(0);
        $mbo_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id])[0];
        $o_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id])[0];

        if ($mbo_student_data) {
            $a_bni_id = [];
            $a_data = [];

            $bni_transaction = false;
            if ($bni_transaction) {
                foreach ($bni_transaction as $o_mdb_bni) {
                    if (!in_array($o_mdb_bni->id, $a_bni_id)) {
                        array_push($a_bni_id, $o_mdb_bni->id);
                    }
                }
            }
            
            if ((!is_null($o_student_data->portal_id)) AND ($o_student_data->portal_id != 0)) {
                $mba_portal_invoice_student = false;
                if ($mba_portal_invoice_student) {
                    foreach ($mba_portal_invoice_student as $o_invoice) {
                        $mba_invoice_sub = false;
                        if ($mba_invoice_sub) {
                            foreach ($mba_invoice_sub as $o_invoice_sub) {
                                $mba_invoice_sub_details = false;
                                if ($mba_invoice_sub_details) {
                                    foreach ($mba_invoice_sub_details as $o_invoice_sub_details) {
                                        $s_payment_code = substr($o_invoice_sub_details->virtual_account, 4, 2);
                                        $mbo_payment_type = false;
                                        $o_bni_transaction = false;
                                        $paid_amount = (is_null($o_invoice_sub_details->amount_paid)) ? ((($o_bni_transaction) ? $o_bni_transaction->payment_amount : '')) : $o_invoice_sub_details->amount_paid;

                                        array_push($a_data, [
                                            'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->name : '',
                                            'billed_amount' => $o_invoice_sub_details->amount,
                                            'description' => $o_invoice_sub_details->description,
                                            'paid_amount' => (($o_bni_transaction) AND ($o_bni_transaction->payment_amount > $paid_amount)) ? $o_bni_transaction->payment_amount : $paid_amount,
                                            'note_invoice' => $o_invoice->notes,
                                            'bni_id' => ($o_bni_transaction) ? $o_bni_transaction->trx_id : '',
                                            'status' => $o_invoice_sub_details->status,
                                            'note_va' => $o_invoice_sub_details->additional_description.' - '.$o_invoice_sub_details->change_details,
                                            'datetime_payment' => ($o_bni_transaction) ? $o_bni_transaction->datetime_payment : '',
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $mba_staging_invoice_data = $this->General->get_where('dt_invoice', ['personal_data_id' => $mbo_student_data->personal_data_id]);
            if ($mba_staging_invoice_data) {
                foreach ($mba_staging_invoice_data as $o_invoice) {
                    $mba_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $o_invoice->invoice_id]);
                    if ($mba_sub_invoice) {
                        foreach ($mba_sub_invoice as $o_sub_invoice) {
                            $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', ['sub_invoice_id' => $o_sub_invoice->sub_invoice_id]);
                            if ($mba_sub_invoice_details) {
                                foreach ($mba_sub_invoice_details as $o_invoice_sub_details) {
                                    $s_payment_code = substr($o_invoice_sub_details->sub_invoice_details_va_number, 4, 2);
                                    $mbo_payment_type = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_payment_code])[0];
                                    array_push($a_data, [
                                        'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->payment_type_name : '',
                                        'billed_amount' => $o_invoice_sub_details->sub_invoice_details_amount,
                                        'description' => $o_invoice_sub_details->sub_invoice_details_description,
                                        'paid_amount' => $o_invoice_sub_details->sub_invoice_details_amount_paid,
                                        'note_invoice' => $o_invoice->invoice_note,
                                        'bni_id' => $o_invoice_sub_details->trx_id,
                                        'status' => $o_invoice->invoice_status,
                                        'note_va' => (!is_null($o_invoice_sub_details->sub_invoice_details_remarks)) ? $o_invoice_sub_details->sub_invoice_details_remarks : '',
                                        'datetime_payment' => $o_invoice_sub_details->sub_invoice_details_datetime_paid_off,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            if (count($a_data) > 0) {
                $a_data = array_values($a_data);

                $s_student_name = str_replace(' ', '_', strtolower($mbo_student_data->personal_data_name));

                $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
                $s_file_name = 'Historical_Invoice_'.$s_student_name;
                $s_filename = $s_file_name.'.xlsx';
                // $s_file_name = str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));
                // $s_folder_master = $s_file_name.'_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$s_class_master_id;

                $s_file_path = APPPATH."uploads/finance/invoice_history/{$mbo_student_data->finance_year_id}/$s_student_name/";

                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                // $s_file_path = $path_master.$s_file_name;
                // $fp = fopen($s_file_path, 'w+');

                // fputcsv($fp, [
                //     'Payment Type', 'Billed Amount', 'Description', 'Paid Amount', 'Datetime Payment'
                // ]);
                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI Finance Services")
                    ->setCategory("Invoice Student Report");

                $i_row = 1;
                $o_sheet->setCellValue('A'.$i_row, 'Payment Type');
                $o_sheet->setCellValue('B'.$i_row, 'Billed Amount');
                $o_sheet->setCellValue('C'.$i_row, 'Description');
                $o_sheet->setCellValue('D'.$i_row, 'Paid Amount');
                $o_sheet->setCellValue('E'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('F'.$i_row, 'Billing Status');
                $o_sheet->setCellValue('G'.$i_row, 'Invoice Note');
                $o_sheet->setCellValue('H'.$i_row, 'Billing Note');
                $o_sheet->setCellValue('J'.$i_row, 'Billing ID');
                $i_row++;

                foreach ($a_data as $data) {
                    // fputcsv($fp, [
                    //     $data['payment_type'],
                    //     $data['billed_amount'],
                    //     $data['description'],
                    //     $data['paid_amount'],
                    //     $data['datetime_payment'],
                    // ]);

                    $o_sheet->setCellValue('A'.$i_row, $data['payment_type']);
                    $o_sheet->setCellValue('B'.$i_row, $data['billed_amount']);
                    $o_sheet->setCellValue('C'.$i_row, $data['description']);
                    $o_sheet->setCellValue('D'.$i_row, $data['paid_amount']);
                    $o_sheet->setCellValue('E'.$i_row, $data['datetime_payment']);
                    $o_sheet->setCellValue('F'.$i_row, $data['status']);
                    $o_sheet->setCellValue('G'.$i_row, $data['note_invoice']);
                    $o_sheet->setCellValue('H'.$i_row, $data['note_va']);
                    $o_sheet->setCellValue('J'.$i_row, $data['bni_id']);
                    $i_row++;
                }

                // fclose($fp);
                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_path_info = pathinfo($s_file_path.$s_filename);
                $s_file_ext = $a_path_info['extension'];
                header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                readfile( $s_file_path.$s_filename );
                exit;
            }
            else{
                print('no data found!');
            }
            // print('<pre>');
            // var_dump($a_data);
            // print('</pre>');
            exit;
        }
        else {
            show_404();
        }
    }

    // public function test()
    // {
    //     $mba_student_invoice_semester = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
    //         'rs.semester_number' => $i,
    //         'df.payment_type_code' => $s_payment_type_code,
    //         'df.study_program_id' => $o_student->study_program_id
    //     ]);
    //     print('<pre>');
    //     var_dump($mba_student_invoice_semester);exit;
    // }

    public function parsing_semester($s_batch, $s_academic_year_id, $s_semester_type_id)
    {
        return (($s_academic_year_id - $s_batch) * 2) + $s_semester_type_id;
    }

    public function get_invoice_student($a_config = ['student_id' => false, 'student_list' => false])
    {
        $mba_student_list_data = false;
        if ($a_config['student_id']) {
            $mba_student_list_data = $this->Stm->get_student_filtered(['ds.student_id' => $a_config['student_id']]);
        }
        else if ($a_config['student_list']) {
            $mba_student_list_data = $a_config['student_list'];
        }
        else if ((!$a_config['student_id']) AND (!$a_config['student_list'])) {
            $mba_student_list_data = $this->Stm->get_student_filtered();
        }

        $a_return_data = [];
        if ($mba_student_list_data) {
            $mbo_active_semester = $this->Smm->get_active_semester();
            $current_date = date('Y-m-d H:i:s');
            $s_academic_year_id = $mbo_active_semester->academic_year_id;
            $s_semester_type_id = $mbo_active_semester->semester_type_id;

            if ($current_date > $mbo_active_semester->semester_end_date) {
                if ($mbo_active_semester->semester_type_id == 2) {
                    $s_academic_year_id = $mbo_active_semester->academic_year_id + 1;
                    $s_semester_type_id = 1;
                }
                else {
                    $s_academic_year_id = $mbo_active_semester->academic_year_id;
                    $s_semester_type_id = 2;
                }
            }
            
            foreach ($mba_student_list_data as $o_student) {
                $a_student_data = [
                    'student_id' => $o_student->student_id,
                    'personal_data_id' => $o_student->personal_data_id,
                    'personal_data_name' => $o_student->personal_data_name,
                    'study_program_name' => $o_student->study_program_name,
                    'study_program_abbreviation' => $o_student->study_program_abbreviation,
                    'faculty_name' => $o_student->faculty_name,
                    'faculty_abbreviation' => $o_student->faculty_abbreviation,
                    'student_number' => $o_student->student_number,
                    'student_status' => $o_student->student_status,
                    'student_type' => $o_student->student_type,
                    'academic_year_id' => $o_student->academic_year_id,
                    'finance_year_id' => $o_student->finance_year_id,
                ];
                
                $i_max_semester = 14;
                $c_semester = $c_max_prev_semester = $c_max_curr_semester = 'J';
                
                for ($i=1; $i <= $i_max_semester ; $i++) {
                    if ($i <= $student_current_semester) {
                        // 
                    }
                }
            }
        }
    }

    public function generate_invoice_report($s_payment_type_code = '02', $mba_student_data = false)
    {
        // $s_payment_type_code = '02';
        // $a_student_status = ['active', 'inactive', 'onleave', 'graduated'];
        $a_student_status = ['active', 'inactive', 'graduated'];
        $mba_portal_payment_type = false;
        // print('<pre>');
        // var_dump($mbo_portal_payment_type);exit;
        $a_student_filter = [
            'ds.academic_year_id >=' => '2015',
            'ds.academic_year_id <=' => '2023',
        ];
        $mba_student_data = $this->Stm->get_student_filtered($a_student_filter, $a_student_status, "ds.academic_year_id ASC, fc.faculty_abbreviation ASC, rsp.study_program_abbreviation ASC, dpd.personal_data_name");
        // print('<pre>');var_dump($mba_student_data);exit;
        if ($mba_student_data) {
            $mbo_active_semester = $this->Smm->get_active_semester();
            $current_date = date('Y-m-d H:i:s');
            $s_academic_year_id = $mbo_active_semester->academic_year_id;
            $s_semester_type_id = $mbo_active_semester->semester_type_id;

            if ($current_date > $mbo_active_semester->semester_end_date) {
                if ($mbo_active_semester->semester_type_id == 2) {
                    $s_academic_year_id = $mbo_active_semester->academic_year_id + 1;
                    $s_semester_type_id = 1;
                }
                else {
                    $s_academic_year_id = $mbo_active_semester->academic_year_id;
                    $s_semester_type_id = 2;
                }
            }

            $s_template_path = APPPATH.'uploads/templates/finance/template_invoice_report_v3.xlsx';

            $s_file_path = APPPATH."uploads/finance/report/semester/".$s_academic_year_id.$s_semester_type_id."/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_file_name = "Tuition_fee_Report_".date('Y-m-d')."";
            $s_filename = $s_file_name.'.xlsx';

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Tuition Fee Billed Semester 1-8");

            $o_sheet->setCellValue('A1', "LIST OF STUDENT $s_academic_year_id-$s_semester_type_id");

            $i_num = 1;
            $i_row = 6;
            $a_row_zero = [];
            $o_sheet->insertNewRowBefore($i_row, 1);
            foreach ($mba_student_data as $o_student) {
                $o_sheet->setCellValue('A'.$i_row, $i_num++);
                $o_sheet->setCellValue('B'.$i_row, $o_student->faculty_abbreviation);
                $o_sheet->setCellValue('C'.$i_row, $o_student->study_program_abbreviation);
                $o_sheet->setCellValue('D'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('E'.$i_row, $o_student->student_number);
                $o_sheet->setCellValue('F'.$i_row, $o_student->student_status);
                $o_sheet->setCellValue('G'.$i_row, $o_student->student_type);
                $o_sheet->setCellValue('H'.$i_row, $o_student->academic_year_id);
                $o_sheet->setCellValue('I'.$i_row, $o_student->finance_year_id);

                $student_current_semester = (($s_academic_year_id - $o_student->academic_year_id) * 2) + $s_semester_type_id;
                // $i_max_semester = (($s_academic_year_id - 2015) * 2) + $s_semester_type_id;
                $i_max_semester = 14;
                $c_semester = $c_max_prev_semester = $c_max_curr_semester = 'J';
                
                // print('<pre>');var_dump(13);exit;
                for ($i=1; $i <= $i_max_semester ; $i++) {
                    if ($i <= $student_current_semester) {
                        if ($i < $student_current_semester) {
                            $c_max_prev_semester = $c_semester;
                        }
                        else if($i == $student_current_semester) {
                            $c_max_curr_semester = $c_semester;
                        }
                        
                        $a_notes = [];

                        if (($i == $student_current_semester) AND ($o_student->student_status == 'onleave')) {
                            $mba_student_invoice_semester = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                                'rs.semester_number' => $i,
                                'df.payment_type_code' => '05'
                            ]);
                        }
                        else {
                            $mba_student_invoice_semester = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                                'rs.semester_number' => $i,
                                'df.payment_type_code' => $s_payment_type_code,
                                'df.study_program_id' => $o_student->study_program_id
                            ]);
                        }

                        $mba_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $i]);
                        if ($mba_semester_data) {
                            $mba_student_semester_data = $this->General->get_where('dt_student_semester', [
                                'student_id' => $o_student->student_id,
                                'semester_id' => $mba_semester_data[0]->semester_id
                            ]);

                            if ($mba_student_semester_data) {
                                if (($mba_student_semester_data) AND ($mba_student_semester_data[0]->student_semester_status == 'onleave')) {
                                    $mba_student_invoice_semester_onleave = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                                        'rs.semester_number' => $i,
                                        'df.payment_type_code' => '05'
                                    ]);

                                    if ($mba_student_invoice_semester_onleave) {
                                        if (!in_array($mba_student_invoice_semester_onleave[0]->invoice_status, ['paid', 'cancelled'])) {
                                            $mba_invoice_full_payment = $this->Im->get_invoice_full_payment($mba_student_invoice_semester_onleave[0]->invoice_id);
                                            array_push($a_notes, 'Invoice Onleave Pending = '.$mba_invoice_full_payment->sub_invoice_details_amount);
                                        }
                                    }
                                }
                            }
                        }

                        if ($i > 8) {
                            $mba_score_data = $this->Scm->get_score_semester([
                                'sm.semester_number' => $i,
                                'sc.student_id' => $o_student->student_id,
                                'sc.score_approval' => 'approved'
                            ]);
                            
                            $i_sks_approved = 0;
                            if ($mba_score_data) {
                                foreach ($mba_score_data as $o_score) {
                                    $i_sks_approved += $o_score->curriculum_subject_credit;
                                }
                            }

                            array_push($a_notes, 'SKS approved = '.$i_sks_approved);
                        }

                        if ($mba_student_invoice_semester) {
                            $i_outstanding = 0;
                            $d_billing = 0;
                            $d_total_fined = 0;
                            $d_total_paid = 0;
                            foreach ($mba_student_invoice_semester as $o_invoice) {
                                // $d_invoice_billing = 0;
                                // $mba_invoice_details_main_data = $this->Im->student_has_invoice_data($o_invoice->personal_data_id, [
                                //     'di.invoice_id' => $o_invoice->invoice_id,
                                //     'df.fee_amount_type' => 'main'
                                // ]);

                                // $mba_invoice_details = $this->Im->get_invoice_details([
                                //     'did.invoice_id' => $o_invoice->invoice_id,
                                // ]);

                                // if (($mba_invoice_details) AND ($mba_invoice_details_main_data)) {
                                //     $d_invoice_billing += $mba_invoice_details_main_data->invoice_details_amount;
                                //     foreach ($mba_invoice_details as $o_details) {
                                //         if ($o_details->fee_amount_type != 'main') {
                                //             if ($o_details->invoice_details_amount_number_type == 'percentage') {
                                //                 $d_amount_details = $mba_invoice_details_main_data->invoice_details_amount * $o_details->invoice_details_amount / 100;
                                //                 if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                //                     $d_invoice_billing += $d_amount_details;
                                //                 }
                                //                 else {
                                //                     $d_invoice_billing -= $d_amount_details;
                                //                 }
                                //             }
                                //             else {
                                //                 if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                //                     $d_invoice_billing += $o_details->invoice_details_amount;
                                //                 }
                                //                 else {
                                //                     $d_invoice_billing -= $o_details->invoice_details_amount;
                                //                 }
                                //             }
                                //         }
                                //     }
                                // }

                                // $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                                // $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);             
                                // if ($mba_invoice_installment) {
                                //     $d_special_billing = 0;
                                //     foreach ($mba_invoice_installment as $o_installment) {
                                //         $d_total_fined += $o_installment->sub_invoice_details_amount_fined;
                                //         if ($o_installment->sub_invoice_details_amount_paid > 0) {
                                //             $d_total_paid += $o_installment->sub_invoice_details_amount_paid;
                                //         }

                                //         if (($o_student->finance_year_id >= 2021) AND ($o_invoice->payment_type_code == '02')) {
                                //             $d_special_billing += $o_installment->sub_invoice_details_amount;
                                //         }
                                //     }

                                //     if (($o_student->finance_year_id >= 2021) AND ($o_invoice->payment_type_code == '02')) {
                                //         $d_invoice_billing = $d_special_billing;
                                //     }
                                    
                                //     if ($mba_invoice_fullpayment) {
                                //         if ($mba_invoice_fullpayment->sub_invoice_details_amount_paid > 0) {
                                //             $d_total_paid += $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                                //         }
                                //     }
                                // }
                                // else if ($mba_invoice_fullpayment) {
                                //     $d_total_paid += $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                                //     $d_total_fined += $mba_invoice_fullpayment->sub_invoice_details_amount_fined;
                                // }

                                // $d_billing += $d_invoice_billing;

                                if (!in_array($o_invoice->invoice_status, ['paid', 'cancelled'])) {
                                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                                    $mba_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                                    if ($mba_invoice_installment) {
                                        foreach ($mba_invoice_installment as $installment) {
                                            if (!in_array($installment->sub_invoice_details_status, ['paid'])) {
                                                $i_outstanding += $installment->sub_invoice_details_amount_total;
                                            }
                                        }
                                    }else if ($mba_invoice_full_payment) {
                                        if ($mba_invoice_full_payment->sub_invoice_details_status != 'paid') {
                                            $i_outstanding += $mba_invoice_full_payment->sub_invoice_details_amount_total;
                                        }
                                    }
                                    // else{
                                        // $o_sheet->setCellValue('H'.$i_row, "");
                                        // $o_sheet->getComment($c_semester.$i_row)->setAuthor('Database');
                                        // $commentRichText = $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun('Notes:');
                                        // $commentRichText->getFont()->setBold(true);
                                        // $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun("\r\n");
                                        // $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun('Tidak ada tagihan.');
                                    // }

                                    if ((!is_null($o_invoice->invoice_note)) OR ($o_invoice->invoice_note != '')) {
                                        array_push($a_notes, trim($o_invoice->invoice_note));
                                    }
                                }
                            }

                            // $d_outstanding = ($d_billing + $d_total_fined) - $d_total_paid;
                            // $d_outstanding = ($d_outstanding < 0) ? 0 : $d_outstanding;
                            $o_sheet->setCellValue($c_semester.$i_row, $i_outstanding);
                        }

                        if (count($a_notes) > 0) {
                            $s_notes = implode("\r\n", $a_notes);
                            $o_sheet->getComment($c_semester.$i_row)->setAuthor('Database');
                            $commentRichText = $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun('Notes:');
                            $commentRichText->getFont()->setBold(true);
                            $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun("\r\n");
                            $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun($s_notes);
                        }
                    }

                    $c_semester++;
                }

                $o_sheet->setCellValue($c_semester.$i_row, "=SUM(J$i_row:W$i_row)");$c_semester++;
                $o_sheet->setCellValue($c_semester.$i_row, "=SUM(J$i_row:".$c_max_prev_semester.$i_row.")");$c_semester++;
                $o_sheet->setCellValue($c_semester.$i_row, "=".$c_max_curr_semester.$i_row);

                $a_record = [];
                $mba_student_record = $this->General->get_where('dt_personal_data_record', ['personal_data_id' => $o_student->personal_data_id, 'record_department' => 'finance']);
                if ($mba_student_record) {
                    foreach ($mba_student_record as $o_record) {
                        array_push($a_record, $o_record->record_comment);
                    }
                }

                if (count($a_record) > 0) {
                    $s_record = implode("\r\n", $a_record);
                    $c_semester++;
                    $o_sheet->setCellValue($c_semester.$i_row, $s_record);
                }

                $total = $o_sheet->getCell('X'.$i_row)->getCalculatedValue();
                if ($total == 0) {
                    if (!in_array($i_row, $a_row_zero)) {
                        array_push($a_row_zero, $i_row);
                    }
                }

                $i_row++;
                $o_sheet->insertNewRowBefore($i_row, 1);
                // else {
                //     // $i_row++;
                //     // $o_sheet->insertNewRowBefore($i_row, 1);
                // }
                // // $i_row++;
                
            }

            // foreach ($a_row_zero as $row_zero) {
            //     print('<pre>');var_dump($row_zero);
            //     // $o_sheet->removeRow($row_zero);
            // }
            // exit;
            $x_row = ($i_row - 1);
            while ($o_sheet->getCell('X'.$x_row)->getValue() !== NULL) {
                $total = $o_sheet->getCell('X'.$x_row)->getCalculatedValue();
                if ($total == 0) {
                    $o_sheet->removeRow($x_row);
                }
                
                $x_row--;
            }
            $n_row = 6;
            $n_number = 1;
            while ($o_sheet->getCell('A'.$n_row)->getValue() !== NULL) {
                $o_sheet->setCellValue('A'.$n_row, $n_number++);
                $o_sheet->setCellValue('X'.$n_row, "=SUM(J$n_row:W$n_row)");

                $n_row++;
            }
            // exit;
            // $o_sheet->removeRow(16);
            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
            $a_return = ['code' => 0, 'filename' => $s_filename, 'semester' => $s_academic_year_id.$s_semester_type_id];
        }else{
            $a_return = ['code' => 1, 'message' => 'No student found!'];
        }

        return $a_return;
    }

    public function check_invoice()
    {
        $mba_invoice_data = $this->Im->get_invoice_list_detail([
            'di.academic_year_id' => '2021',
            'di.semester_type_id' => '2'
        ]);

        if ($mba_invoice_data) {
            print('<table border="1">');
            print('<tr><td>Student Name</td><td>Study Program</td><td>Semester</td><td>Billed Amount</td></tr>');
            foreach ($mba_invoice_data as $o_invoice) {
                $mba_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_invoice->study_program_id]);
                $o_invoice_full = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                print('<tr>');
                print('<td>'.$o_invoice->personal_data_name.'</td>');
                print('<td>'.$mba_prodi[0]->study_program_abbreviation.'</td>');
                print('<td>'.$o_invoice->semester_id.'</td>');
                print('<td>'.$o_invoice_full->sub_invoice_details_amount_total.'</td>');
                // print('<td>'.$o_invoice->personal_data_name.'</td>');
                print('</tr>');
                // print('<pre>');var_dump($o_invoice);exit;
            }
            print('</table>');
        }
    }

    public function fill_semester_setting()
    {
        $this->load->model('academic/Semester_model', 'Sm');
        $a_student_id = ["4fc42b28-2fd7-4dde-889e-01878cbfd001","8b6a3c4b-5fd6-4376-813e-64eabb1524d8","b1629d2e-4756-4c1d-80d4-974d6ac4e4e5","c653c7cb-8b50-4932-8a84-14a3ff5bc23a","2313daf0-9381-4bad-904d-be871946385f","39b0bab0-252f-4404-8e37-ec8c0704ec18","d434594a-6cf5-4957-adb3-53df3c03356f","7883b270-9115-40fe-8443-166397800899","07042d4b-ff95-4e51-93da-934d9cf582f0","0a37b22f-c5c5-4e06-8e40-8f1fc7ea9efc","3004dc42-e841-492c-ae85-3365dbcb4db0","80247689-664f-4468-95cd-feaf5c5e6879","f6840372-41d6-4b50-86d1-90b7a85a7fc7","ea9620f1-99b3-46e9-9fb9-42ab062ac5e1","06c2a5c4-a808-4730-873e-64e16e08653d","55372647-a67e-4277-b5b4-56d39f6fda9f","8c9eb463-5210-4533-9f93-b8a36eba9f2e","42ecf50c-568d-4d27-be0d-a9d871ccaf31","87a6a643-7d03-41d3-a7a2-9c6934dc8cbe","9175505b-ad16-41ba-b460-3affc223cea2","02807601-692c-45f4-9fbd-a472f90e0c50","37d143f4-97f6-4418-b94a-9885af8e0b35","9fea31ac-a915-4377-b7da-8cfab9e6a7c0","f19f2138-e4fa-4e76-b54a-76bfc6715039","4b65c225-11a4-4fdd-bcc2-e9d183a89738","557d1732-e17a-4320-8f96-7dcaf15d3d31","e616fd1d-a82f-4e99-926c-66a2ecaa398e","4a79a006-75e7-4a31-94d9-11baca772478","cc14325f-e254-4835-8416-cb8850cd820a","4d05db35-e839-49c4-9bd3-da8d02c87cdc","4ead538b-e011-4b93-aa15-a7e170df9702","5f5653fc-1a6a-4843-910d-f76c2f45192f","20d33e2e-bf88-412b-8af8-3de1c21c45c6","cb61f461-9488-4eb2-ac6e-88bc9cde8f6e","e5b5de99-c2c0-4f67-bcd2-5b419b8df219","4d0c8dd9-7b8c-42d1-a87d-fd0d959107a7","d6880f5a-0453-4e0d-8b70-6e33ebd6f308","410c517e-7f41-49b2-a80b-a58ae1fedf5e","bae661ea-f406-4b3c-b638-e3adce0f289b","f6394ebb-23d7-4b6e-84ba-c165ebd89c1f","8d2cf70e-a69e-48ba-9e53-e25affd44888","dd4e66c9-7ef8-43ae-b130-f674e846fd5d","2795061c-b327-44f0-93bd-f40c0e3a93b5","62a6e400-3479-4434-989c-dbe17c555815","9391fdf4-a1d1-4e6f-b155-747feddb5928","c863f7f3-69b2-44a0-ad41-d8029b6368fc","e718cfaa-0fb2-4741-8077-e1835d8688e9","1d34a31e-60db-43e4-874c-7d5321e18d73","37267ee6-40e3-4592-bb9b-4d2836842618","7999e04b-6982-441c-8704-e95bcc303ad5","5bb6ec2a-fddb-44a3-8a75-40360260345d","93a44594-9ce0-40cc-8d9b-63b4de2a6de7","969076df-5d9b-423b-b2cb-61bde4b8b5ad","ab33d9cf-4102-4289-8c2f-2e55a890da36","24368980-da1b-4df6-a360-cdbd0daca394","922ad0bc-0da1-4433-b2f4-904d28f1c34b","b3fdc0f5-9bf5-43fa-a832-92d71aeb1f3c","775cb336-36bb-4aab-a236-8a036e67fee0","9f4b1c26-b0da-4477-a8de-36ffba4db2a9","e671e3f9-e59b-459f-a086-c05ce89572a1","9964348b-87e4-4b1d-acdc-1dea42b972df","60b3b411-0c7e-4983-bfcc-c7ef45455ba6"];
        foreach ($a_student_id as $s_student_id) {
            $mba_student_semester_data = $this->Sm->get_semester_student_personal_data(['dss.student_id' => $s_student_id]);
            if ($mba_student_semester_data) {
                $i_must_semester = 0;
                foreach ($mba_student_semester_data as $o_student_semester) {
                    if (in_array($o_student_semester->semester_type_id, [1,2])) {
                        if ($i_must_semester == 0) {
                            $i_must_semester = $o_student_semester->semester_id;
                        }
                        else {
                            $i_must_semester++;
                        }
    
                        if ($i_must_semester != $o_student_semester->semester_id) {
                            print('<pre>');
                            var_dump($o_student_semester);exit;
                        }
                    }
                }
            }
            // print('<pre>');
            // var_dump($mba_student_semester_data);exit;
        }
    }

    function get_minimum_payment_collective() {
        $a_student_number = ['11202002006','11202002004','11202002005'];
        print('<table border="1">');
        print('<tr><th>Student Name</th><th>NIM</th><th>VA Number</th><th>Minimum Payment</th></tr>');
        foreach ($a_student_number as $s_student_number) {
            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number, 'ds.student_status !=' => 'resign']);
            if ($mba_student_data) {
                $o_student = $mba_student_data[0];

                $s_va_number = $this->Bnim->generate_va_number(
                    '02',
                    'student', 
                    $o_student->student_number, 
                    $o_student->finance_year_id,
                    $o_student->program_id
                );

                $amount = modules::run('callback/api/get_minimum_payment', $s_va_number);
                print('<tr>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td>'.$o_student->student_number.'</td>');
                print('<td>'.$s_va_number.'</td>');
                print('<td>'.$amount.'</td>');
                print('</tr>');
            }
        }
        print('</table>');
    }

    function list_icon() {
        // print(FCPATH);exit;
        $font_vars = file_get_contents(FCPATH.'assets/vendors/fontawesome/scss/_variables.scss');
        $start_regular_pos = strpos($font_vars, '$fa-var-500px');
        $end_regular_pos = strpos($font_vars, '$fa-var-zhihu');

        // $start_brand_pos = strpos($font_vars, '$fa-var-youtube-square');
        // $end_brand_pos = strlen($font_vars);
        // $end_brand_pos = strpos($font_vars, (strlen($font_vars)));

        // $font_vars = substr($font_vars, $pos, ($posend - $pos - 2));
        $font_regular_icon = substr($font_vars, ($start_regular_pos), ($end_regular_pos - $start_regular_pos - 2));
        // $font_brand_icon = substr($font_vars, $start_brand_pos, ($end_brand_pos - $start_brand_pos - 4));

        $lines_regular = explode("\n", $font_regular_icon);
        // $lines_brand = explode("\n", $font_brand_icon);
        
        $fonts_list = array();
        foreach($lines_regular as $key => $line) {
            if ($key > 0) {
                if(strpos($line, ':') !== false) {
                    $t = explode(":", $line);
                    $f = 'fa '.trim(str_replace('$fa-var', 'fa', $t[0]));
                    $f = str_replace(",", "", $f);
                    $f = str_replace(";", "", $f);
                    if (!in_array($f, $fonts_list)) {
                        // print('<pre>');var_dump($f);exit;
                        array_push($fonts_list, $f);
                    }
                }
            }
        }
        
        // foreach($lines_brand as $key => $line) {
        //     if ($key > 0) {
        //         if(strpos($line, ':') !== false) {
        //             $t = explode(":", $line);
        //             $f = 'fab '.trim(str_replace('$fa-var', 'fa', $t[1]));
        //             $f = str_replace(",", "", $f);
        //             if (!in_array($f, $fonts_list)) {
        //                 array_push($fonts_list, $f);
        //             }
        //         }
        //     }
        // }
        // print('<pre>');var_dump($fonts_list);exit;
        $this->a_page_data['fontlist'] = $fonts_list;
        $this->a_page_data['body'] = $this->load->view('devs/icon_list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
    }

    function test_() {
        $this->load->model('access/Log_model', 'Lom');
        $data = $this->Lom->findsearch([
            'access_log_method' => 'save_registration_study_plan'
        ], [
            'access_log_post_data' => '25b4d778-a69a-4560-9344-29c3be274f55'
        ]);
        $execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        print('Execution Time: '.$execution_time.' second');
        print('<pre>');var_dump($data);exit;
    }

    public function invoice_semester($s_academic_year_id, $s_semester_type_id)
    {
        $a_student_semester_lewat = [
            // '92f228ee-875f-460e-a273-78a35164a266',
            // '3898a159-e5b1-4aa7-9f56-fe755db53fc6',
            // '971ec46b-09f1-4062-961c-c16283dadc26',
            // '799b7498-1238-428d-94b0-4440f9f578c2',
            // 'a33a4ed2-0fea-4ac1-9eff-4e29f1148167',
            // "06d8a1f2-0c24-4cb3-bd54-25038c150b0f","2534d8e7-337a-48c6-90ab-0fc7c0f28e9d","93676d76-d350-47df-9855-c54b44907fc2","211c6064-b609-440c-a03f-8a3414c9290d","19bb6b73-72ca-4e21-a9ed-74d1e4b287d9","e0783a2a-56e8-46d5-863d-b76d70469104","71d1f931-dad0-4891-a45e-ea00ed924bff","c09b201a-fb1d-4383-9e80-ee00a782b90a","ffa77640-caff-4c30-aad9-2a61058303b1","c4812cc9-c71c-4055-8ccd-bb31ad6289ac"
        ];

        $a_student_exchange = [
            'e08882fd-476e-451b-a927-a2436a8947c0', '241467b7-ec27-4ff7-b7f1-63acd5730bda', '671b3434-f57e-4532-9c73-f2d41b31f2b2',
            'bfe83288-7eaf-44bf-9118-46b26bd6f10b'
        ];

        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_student_finance_year = ($s_academic_year_id + 1) - 8;
        $mba_student_data = $this->Stm->get_student_filtered([
            // 'finance_year_id <=' => $s_academic_year_id
            'finance_year_id >' => $s_student_finance_year
        ], ['active','inactive','onleave']);
        // ], ['active','inactive','onleave']);

        if ($mba_student_data) {
            // $s_file_name = 'TuitionFee_Academic_Year_'.$s_academic_year_id.'-'.$s_semester_type_id.'_Semester_8_keatas_'.date('Y-m-d_H_i');
            $s_file_name = 'TuitionFee_Academic_Year_'.$s_academic_year_id.'-'.$s_semester_type_id.'_'.date('Y-m-d_H_i');
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Tuition Fee Billed Semester 1-8");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Student Number");
            $o_sheet->setCellValue('C1', "Status");
            $o_sheet->setCellValue('D1', "Batch");
            $o_sheet->setCellValue('E1', "Year In");
            $o_sheet->setCellValue('F1', "Faculty");
            $o_sheet->setCellValue('G1', "Study Program");
            $o_sheet->setCellValue('H1', "Semester");
            // $o_sheet->setCellValue('I1', "Semester Setting");
            $o_sheet->setCellValue('I1', "Semester Leave");
            // $o_sheet->setCellValue('K1', "Semester Inactive");
            $o_sheet->setCellValue('J1', "Discount/Scholarship");
            $o_sheet->setCellValue('K1', "SKS Approved");
            $o_sheet->setCellValue('L1', "Billed Semester");
            $o_sheet->setCellValue('M1', "Flyer Amount");
            $o_sheet->setCellValue('N1', "Billed Amount");
            $o_sheet->setCellValue('O1', "Total Paid");
            $o_sheet->setCellValue('P1', "Invoice Note");
            $o_sheet->setCellValue('Q1', "Student Notes");

            $i_row = 2;
            $a_semester_setting_not_found = [];
            $a_fee_notfound = [];
            $i_numcount = 1;
            foreach ($mba_student_data as $o_student) {
                // if ($o_student->personal_data_id == 'd92fa321-e6a0-466d-affc-bd48b68b2d4d') {
                //     print('<pre>');var_dump($o_student);exit;
                // }
                if (!in_array($o_student->personal_data_id, $a_student_semester_lewat)) {
                    // if ($o_student->student_mark_completed_defense != 1) {
                        $mba_is_exchange_student = $this->General->get_where('dt_student_exchange', [
                            'student_id' => $o_student->student_id,
                            'exchange_type' => 'in'
                        ]);

                        $mba_score_data = $this->Scm->get_score_data([
                            'sc.academic_year_id' => $s_academic_year_id,
                            'sc.semester_type_id' => $s_semester_type_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved'
                        ]);
    
                        $mbo_semester_setting_data = $this->General->get_where('dt_student_semester', [
                            'student_id' => $o_student->student_id,
                            'academic_year_id' => $s_academic_year_id,
                            'semester_type_id' => $s_semester_type_id
                        ])[0];
    
                        if (!$mbo_semester_setting_data) {
                            // array_push($a_semester_setting_not_found, $o_student->student_id);
                            // print($i_numcount++.'. semester setting for student_id '.$o_student->student_id.' not available for batch '.$o_student->finance_year_id.'<br>');
                            // // exit;
                            // continue;
                        }
    
                        if (($mbo_semester_setting_data) AND (!is_null($mbo_semester_setting_data->semester_id))) {
                            $i_total_sks = 0;
                            $mbo_semester_score = $this->General->get_where('ref_semester', ['semester_id' => $mbo_semester_setting_data->semester_id])[0];
                            if ($mba_score_data) {
                                foreach ($mba_score_data as $o_score) {
                                    $i_total_sks += $o_score->curriculum_subject_credit;
                                }
                            }
                            $i_current_semester_id = $mbo_semester_setting_data->semester_id;
                            $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $i_current_semester_id])[0];
                            $i_current_semester_number = $mbo_semester_data->semester_number;
                            $b_has_created = false;
                            
                            $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                            $o_sheet->setCellValue('B'.$i_row, $o_student->student_number);
                            $o_sheet->setCellValue('C'.$i_row, $o_student->student_status);
                            $o_sheet->setCellValue('D'.$i_row, $o_student->academic_year_id);
                            $o_sheet->setCellValue('E'.$i_row, $o_student->finance_year_id);
                            $o_sheet->setCellValue('F'.$i_row, $o_student->faculty_abbreviation);
                            $o_sheet->setCellValue('G'.$i_row, $o_student->study_program_abbreviation);
                            $o_sheet->setCellValue('H'.$i_row, $mbo_semester_score->semester_number);
                            // $o_sheet->setCellValue('I'.$i_row, $mbo_semester_setting_data->student_semester_status);
                            $o_sheet->setCellValue('K'.$i_row, $i_total_sks);
                            
                            $mba_student_semester = $this->Smm->get_semester_student($o_student->student_id, ['ss.student_semester_status' => 'onleave'], [1,2]);
                            // $mba_student_semester_inactive = $this->Smm->get_semester_student($o_student->student_id, ['ss.student_semester_status' => 'inactive'], [1,2]);
                            
                            $a_semester_id_leave = [];
                            $a_semester_number_leave = [];
    
                            // $a_semester_id_inactive = [];
                            // $a_semester_number_inactive = [];
    
                            if ($mba_student_semester) {
                                foreach ($mba_student_semester as $o_student_semester) {
                                    if ($o_student_semester->semester_year_id <= $s_academic_year_id) {
                                        if (is_null($o_student_semester->semester_id)) {
                                            print('no semester<pre>');
                                            var_dump($o_student_semester);exit;
                                        }
        
                                        $mbo_semester_data_leave = $this->General->get_where('ref_semester', ['semester_id' => $o_student_semester->semester_id])[0];
                                        if (!in_array($o_student_semester->semester_id, $a_semester_id_leave)) {
                                            array_push($a_semester_id_leave, $o_student_semester->semester_id);
                                            array_push($a_semester_number_leave, $mbo_semester_data_leave->semester_number);
                                        }
                                    }
                                    
                                }
                            }
    
                            if (count($a_semester_number_leave) > 0) {
                                $i_current_semester_number = $i_current_semester_number - count($a_semester_number_leave);
                                $mbo_semester_data_current = $this->General->get_where('ref_semester', ['semester_number' => $i_current_semester_number])[0];
                                $i_current_semester_id = $mbo_semester_data_current->semester_id;
                            }
    
                            // if ($mba_student_semester_inactive) {
                            //     foreach ($mba_student_semester_inactive as $o_student_semester) {
                            //         if ($o_student_semester->semester_year_id <= $s_academic_year_id) {
                            //             if (is_null($o_student_semester->semester_id)) {
                            //                 print('<pre>');
                            //                 var_dump($o_student_semester);exit;
                            //             }
        
                            //             $mbo_semester_data_leave = $this->General->get_where('ref_semester', ['semester_id' => $o_student_semester->semester_id])[0];
                            //             if (!in_array($o_student_semester->semester_id, $a_semester_id_inactive)) {
                            //                 array_push($a_semester_id_inactive, $o_student_semester->semester_id);
                            //                 array_push($a_semester_number_inactive, $mbo_semester_data_leave->semester_number);
                            //             }
                            //         }
                                    
                            //     }
                            // }
                            // $a_data['semester_id_leave'] = implode(',', $a_semester_id_leave);
                            $o_sheet->setCellValue('I'.$i_row, implode(', ', $a_semester_number_leave));
                            // $o_sheet->setCellValue('K'.$i_row, implode(', ', $a_semester_number_inactive));
    
                            $a_scholarship_id = [];
                            $a_siblings = [];
                            $a_amount_discount = [];
                            $a_scholarship_name = [];
                            $a_discount_detail = [];
    
                            if ($mbo_semester_score->semester_number <= 8) {
                                $mba_scholarship_data = $this->General->get_where('dt_personal_data_scholarship', [
                                    'personal_data_id' => $o_student->personal_data_id,
                                    'scholarship_status' => 'active'
                                ]);
    
                                if ($mba_scholarship_data) {
                                    foreach ($mba_scholarship_data as $o_ps) {
                                        $mbo_scholarship_data = $this->General->get_where('ref_scholarship', ['scholarship_id' => $o_ps->scholarship_id, 'scholarship_fee_type' => 'main'])[0];
                                        $mbo_scholarship_detail = $this->General->get_where('ref_scholarship', ['scholarship_id' => $o_ps->scholarship_id]);
                                        // if ($o_student->student_number == '11202207003') {
                                        //     print('<pre>');var_dump($o_ps);exit;
                                        // }
                                        if ($mbo_scholarship_data) {
                                            if (!in_array($o_ps->scholarship_id, $a_scholarship_id)) {
                                                array_push($a_scholarship_id, $o_ps->scholarship_id);
                                                array_push($a_scholarship_name, $mbo_scholarship_data->scholarship_name);
                                            }
                                        }
                                        else if (!is_null($o_ps->personal_data_id_sibling_with)) {
                                            $mba_sibling = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_ps->personal_data_id_sibling_with]);
                                            $s_sibling = 'sibling with '.ucwords(strtolower($mba_sibling[0]->personal_data_name));
                                            if (!in_array($s_sibling, $a_scholarship_name)) {
                                                array_push($a_scholarship_name, $s_sibling);
                                                array_push($a_siblings, $s_sibling);
                                            }
                                        }
                                        else if (!is_null($o_ps->scholarship_fee_id)) {
                                            $mba_fee_data = $this->General->get_where('dt_fee', ['fee_id' => $o_ps->scholarship_fee_id]);
                                            if ($mba_fee_data) {
                                                $o_fee_data = $mba_fee_data[0];
                                                $s_discount = $o_fee_data->fee_description;
                                                if (!in_array($s_discount, $a_scholarship_name)) {
                                                    array_push($a_scholarship_name, $s_discount);
                                                    array_push($a_amount_discount, $o_fee_data->fee_amount);
                                                }
                                            }
                                        }
                                        else if (($mbo_scholarship_detail) AND ($mbo_scholarship_detail[0]->cut_of_tuition_fee == 'yes')) {
                                            array_push($a_scholarship_name, $mbo_scholarship_detail[0]->scholarship_name);
                                        }
                                    }
                                }
                                $o_sheet->setCellValue('J'.$i_row, implode(', ', $a_scholarship_name));
                            }
    
                            $mba_has_invoice = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                                'di.academic_year_id' => $s_academic_year_id,
                                'di.semester_type_id' => $s_semester_type_id,
                                'di.invoice_status != ' => 'cancelled',
                                'df.payment_type_code' => '02',
                                'df.fee_amount_type' => 'main',
                                'df.fee_special' => 'false'
                            ]);
                            
                            if ($mba_has_invoice) {
                                $i_current_semester_id = $mba_has_invoice->semester_id;
                                $mbo_semester_data_ = $this->General->get_where('ref_semester', ['semester_id' => $i_current_semester_id])[0];
                                $i_current_semester_number = $mbo_semester_data_->semester_number;
                            }
    
                            if ($i_current_semester_number > 8) {
                                $i_current_semester_number = $mbo_semester_score->semester_number;
                            }
    
                            $o_sheet->setCellValue('L'.$i_row, $i_current_semester_number);
    
                            $a_filter_fee = [
                                'payment_type_code' => '02', 
                                'program_id' => $o_student->student_program,
                                'study_program_id' => $o_student->study_program_id,
                                'academic_year_id' => $o_student->finance_year_id,
                                'fee_amount_type' => 'main',
                                'semester_id' => $i_current_semester_id,
                                'fee_special' => 'false'
                            ];
    
                            if (count($a_scholarship_id) > 0) {
                                $a_filter_fee['scholarship_id != '] = NULL;
                            }else{
                                $a_filter_fee['scholarship_id'] = NULL;
                            }
    
                            $mbo_fee_semester = $this->Im->get_fee($a_filter_fee);
    
                            // if ($o_student->personal_data_id == 'd92fa321-e6a0-466d-affc-bd48b68b2d4d') {
                            //     print('<pre>');var_dump($a_filter_fee);
                            //     print('<pre>');var_dump($o_student);exit;
                            // }
                            
                            if ($mbo_fee_semester) {
                                $mbo_fee_semester = $mbo_fee_semester[0];
                                $mbo_invoice = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                                    'df.fee_id' => $mbo_fee_semester->fee_id
                                ]);
                                // if ($o_student->student_id == '24368980-da1b-4df6-a360-cdbd0daca394') {
                                //     print('<pre>');
                                //     var_dump($mbo_fee_semester);exit;
                                // }
    
                                $i_amount_paid = 0;
                                $total_billedresult = modules::run('finance/invoice/calculate_billing_student', $o_student->personal_data_id, $i_current_semester_number, 0, $mbo_fee_semester->fee_id, $s_academic_year_id, $s_semester_type_id);
                                if (count($a_scholarship_id) > 0) {
                                    $total_billedresult = $mbo_fee_semester->fee_amount;
                                }
                                
                                $o_sheet->setCellValue('Z'.$i_row, $mbo_fee_semester->fee_id);
                                $o_sheet->setCellValue('M'.$i_row, $mbo_fee_semester->fee_amount);
                                $o_sheet->setCellValue('N'.$i_row, $total_billedresult);
                                // if ($i_current_semester_number > 8) {
                                //     $o_sheet->setCellValue('N'.$i_row, "=K$i_row*M$i_row");
                                // }

                                if ($mba_has_invoice) {
                                    $mbo_invoice = $mba_has_invoice;
                                    $mba_invocie_details_data = $this->Im->get_student_billing(['di.invoice_id' => $mbo_invoice->invoice_id]);
                                    // print('no detail invoice:'.$mbo_invoice->invoice_id.'<pre>');var_dump($mba_invocie_details_data);exit;
                                    $mbo_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $mbo_invoice->invoice_id])[0];
                                    if ($mbo_invoice->invoice_status != 'paid') {
                                        // $mba_invoice_installment = $this->Im->get_invoice_installment($mbo_invoice->invoice_id);
                                        $mba_invoice_installment = $this->Im->get_invoice_installment($mbo_invoice->invoice_id);
    
                                        if ($mba_invoice_installment) {
                                            foreach ($mba_invoice_installment as $o_installment) {
                                                $i_amount_paid += $o_installment->sub_invoice_details_amount_paid;
                                            }
                                        }
                                    }else{
                                        $i_amount_paid = $mbo_sub_invoice->sub_invoice_amount;
                                    }
                                    $d_total_detail_amount = $mbo_invoice->invoice_details_amount;
                                    $d_billing_detail_amount = $d_total_detail_amount;
                                    if ($mba_invocie_details_data) {
                                        foreach ($mba_invocie_details_data as $o_details) {
                                            if ($o_details->fee_amount_type != 'main') {
                                                if ($o_details->invoice_details_amount_number_type == 'percentage') {
                                                    $d_amount_details = $d_total_detail_amount * $o_details->invoice_details_amount / 100;
                                                    if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                                        $d_billing_detail_amount += $d_amount_details;
                                                    }
                                                    else {
                                                        $d_billing_detail_amount -= $d_amount_details;
                                                    }
                                                }
                                                else {
                                                    if ($o_details->invoice_details_amount_sign_type == 'positive') {
                                                        $d_billing_detail_amount += $o_details->invoice_details_amount;
                                                    }
                                                    else {
                                                        $d_billing_detail_amount -= $o_details->invoice_details_amount;
                                                    }
                                                }
                                            }
                                        }
                                    }
    
                                    $o_sheet->setCellValue('N'.$i_row, $d_billing_detail_amount);
                                    $o_sheet->setCellValue('P'.$i_row, $mbo_invoice->invoice_note);
                                    $o_sheet->getStyle('A'.$i_row.':O'.$i_row)->getFill()
                                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                        ->getStartColor()->setARGB('40BA30');
    
                                    if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                                        $o_sheet->setCellValue('S'.$i_row, $mbo_invoice->invoice_id);
                                    }
                                }else if ($i_current_semester_number > 8) {
                                    $total_billed = ($i_total_sks > 0) ? $mbo_fee_semester->fee_amount * $i_total_sks : 0;
                                    $o_sheet->setCellValue('N'.$i_row, $total_billed);
                                }

                                if ($mba_is_exchange_student) {
                                    $o_sheet->getStyle('A'.$i_row.':O'.$i_row)->getFill()
                                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                        ->getStartColor()->setARGB('000000');
                                }

                                $d_amount_fee = $mbo_fee_semester->fee_amount;
                                if (count($a_siblings) > 0) {
                                    $d_amount_fee = $d_amount_fee / 2;
                                }

                                if (count($a_amount_discount) > 0) {
                                    foreach ($a_amount_discount as $d_discount) {
                                        $d_amount_fee = $d_amount_fee - $d_discount;
                                    }
                                }
    
                                $o_sheet->setCellValue('O'.$i_row, $i_amount_paid);
    
                                // $o_sheet->setCellValue('H1', "Semester");
                                // $o_sheet->setCellValue('I1', "Semester Leave");
                                // $o_sheet->setCellValue('J1', "Discount/Scholarship");
                                // $o_sheet->setCellValue('K1', "SKS Approved");
                                // $o_sheet->setCellValue('L1', "Billed Semester");
                                // $o_sheet->setCellValue('M1', "Flyer Amount");
                                // $o_sheet->setCellValue('N1', "Billed Amount");
                                // $o_sheet->setCellValue('O1', "Total Paid");
                                // $o_sheet->setCellValue('P1', "Invoice Note");
                                // $o_sheet->setCellValue('Q1', "Student Notes");
    
                                $a_notes = [];
                                $mba_student_notes = $this->General->get_where('dt_personal_data_record', ['personal_data_id' => $o_student->personal_data_id]);
                                if ($mba_student_notes) {
                                    foreach ($mba_student_notes as $o_notes) {
                                        array_push($a_notes, $o_notes->record_comment);
                                    }
                                }
    
                                if (count($a_notes) > 0) {
                                    $o_sheet->setCellValue('Q'.$i_row, implode('; ', $a_notes));
                                }
                            }else{
                                // print('<pre>');var_dump($o_student);
                                // print('fee not found for '.json_encode($a_filter_fee));exit;
                                array_push($a_fee_notfound, "fee not found for ".json_encode($a_filter_fee));
                            }

                            $i_row++;
                        }
                        else{
                            // print('no semester student '.$o_student->personal_data_name.' - '.$o_student->student_id);exit;
                        }
    
                        // $i_row++;
                    // }
                }
            }

            if (count($a_semester_setting_not_found) > 0) {
                // print('<pre>');var_dump($a_semester_setting_not_found);exit;
                print('"'.implode('","', $a_semester_setting_not_found));exit;
            }
            else if (count($a_fee_notfound) > 0) {
                print(implode('<br>', $a_fee_notfound));exit;
            }

            $c_col = 'A';
            for ($i = 1; $i < 50; $i++) { 
                $o_sheet->getColumnDimension($c_col++)->setAutoSize(true);
            }
            // $o_sheet->getStyle('A1:N'.($i_row-1))->applyFromArray($style_border);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    function get_graduate_student($s_graduate_year_id) {
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.graduated_year_id' => $s_graduate_year_id
        ]);
        if ($mba_student_data) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'student_graduation';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/temp/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI IT Services")
                ->setCategory("Student Graduation 2023");

            $o_sheet->setCellValue('A1', "id_registrasi_mahasiswa");
            $o_sheet->setCellValue('B1', "id_jenis_keluar");
            $o_sheet->setCellValue('C1', "tanggal_keluar");
            $o_sheet->setCellValue('D1', "id_periode_keluar");
            $o_sheet->setCellValue('E1', "nomor_sk_yudisium");
            $o_sheet->setCellValue('F1', "tanggal_sk_yudisium");
            $o_sheet->setCellValue('G1', "ipk");
            $o_sheet->setCellValue('H1', "nomor_ijazah");
            $o_sheet->setCellValue('I1', "judul_skripsi");
            $o_sheet->setCellValue('J1', "nama_mahasiswa");
            $i_row = 2;
            foreach ($mba_student_data as $o_student) {
                $mba_score_student = $this->Scm->get_score_data([
                    'sc.student_id' => $o_student->student_id,
                    'sc.score_approval' => 'approved',
                    'sc.score_display' => 'TRUE',
                    'curs.curriculum_subject_type != ' => 'extracurricular',
                    'curs.curriculum_subject_credit >' => 0
                ]);

                $d_gpa = 0;
                if ($mba_score_student) {
                    $a_sks = [];
                    $a_merit = [];
                    foreach ($mba_score_student as $o_score) {
                        $d_gp = $this->grades->get_grade_point($o_score->score_sum);
                        $d_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $d_gp);

                        array_push($a_sks, $o_score->curriculum_subject_credit);
                        array_push($a_merit, $d_merit);
                    }

                    $d_total_sks = array_sum($a_sks);
                    $d_total_merit = array_sum($a_merit);

                    $d_gpa = $this->grades->get_ipk($d_total_merit, $d_total_sks);
                }
                $o_sheet->setCellValue('A'.$i_row, $o_student->student_id);
                $o_sheet->setCellValue('B'.$i_row, "1");
                $o_sheet->setCellValue('C'.$i_row, $o_student->student_date_graduated);
                $o_sheet->setCellValue('D'.$i_row, "20222");
                $o_sheet->setCellValue('E'.$i_row, "SK/REC/1934/IULI/IX/2023");
                $o_sheet->setCellValue('F'.$i_row, "2023-09-22");
                $o_sheet->setCellValue('G'.$i_row, $d_gpa);
                $o_sheet->setCellValue('H'.$i_row, $o_student->student_pin_number);
                $o_sheet->setCellValue('I'.$i_row, $o_student->student_thesis_title);
                $o_sheet->setCellValue('J'.$i_row, $o_student->personal_data_name);

                $i_row++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    public function get_unpaid_invoice_student()
    {
        $a_list_student = ['11201701018','11202107006','11202009003','11201703004','11201507006','11201507014','11201901001','11201807015','11201807012','11201807004','11202001003','11202102012','11201902005','11202002013','11201602002','11201807002','11201807006','11201808009','11201804002','11201801006','11201701014','11201607017','11202007010','11201602031','11202007021','11202002018','11202101011','11201907013','11201803007','11201706001','11202101010','11201801014','11201705006','11201608005','11202007004','11201802010','11201901010','11202003007','11201608008','11202102013','11201807013','11202001018','11201908003','11201711007','11202101006','11202107002','11201908002','11202102006','11202008012','11201901009','11201601024','11202005002','11201807009','11202101013','11201610004','11201701020','11201505006','11201811001','11201807008'];
        $a_student_processed = [];
        $i = 1;
        $mba_invoice_list = $this->Im->get_unpaid_invoice(['di.invoice_status != ' => 'paid']);
        if ($mba_invoice_list) {
            // print('<table>');
            foreach ($mba_invoice_list as $o_invoice) {
                if ($o_invoice->invoice_status != 'cancelled') {
                    $mba_student_data = $this->Stm->get_student_filtered(['finance_year_id <=' => 2022, 'ds.personal_data_id' => $o_invoice->personal_data_id], ['active', 'graduated']);
                    if (($mba_student_data) AND (in_array($mba_student_data[0]->student_number, $a_list_student))) {
                        $mba_invoice_details = $this->Im->get_invoice_list_detail(['di.invoice_id' => $o_invoice->invoice_id]);
                        if ($mba_invoice_details) {
                            $o_invoice_data = $mba_invoice_details[0];
                            if ($o_invoice_data->payment_type_code == '02') {
                                // if (!in_array($mba_student_data[0]->student_id, $a_student_processed)) {
                                //     array_push($a_student_processed, $mba_student_data[0]->student_id);
                                //     $mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_invoice_data->semester_id])[0];
                                //     $table_list = '<tr>';
                                //     $table_list .= '<td>'.$i++.'</td><td>'.$o_invoice->personal_data_name.'</td><td>'.$mba_student_data[0]->academic_year_id.'</td><td>'.$mba_semester_data->semester_number.'</td><td>'.$o_invoice->invoice_description.'</td><td>'.$o_invoice->invoice_id.'</td>';
                                //     $table_list .= '</tr>';
                                //     print($table_list);
                                // }
                                $mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_invoice_data->semester_id])[0];
                                $mba_sub_invoice_details = $this->Im->get_invoice_data(['di.invoice_id' => $o_invoice->invoice_id]);
                                if (!$mba_sub_invoice_details) {
                                    print('invoice details not found!'.$o_invoice->invoice_id);exit;
                                }
                                
                                foreach ($mba_sub_invoice_details as $o_sub_invoice_details) {
                                    if (($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($o_sub_invoice_details->sub_invoice_details_amount_paid == 0)) {
                                        print($o_sub_invoice_details->trx_id);
                                        print(' / ');

                                        $a_sub_invoice_details_update = [
                                            'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime('2022-06-28'))
                                        ];
                                        $this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id]);

                                        modules::run('finance/invoice/change_trx_details', $o_sub_invoice_details->trx_id);
                                    }
                                }

                                // $table_list = '<tr>';
                                // $table_list .= '<td>'.$i++.'</td><td>'.$o_invoice->personal_data_name.'</td><td>'.$mba_student_data[0]->academic_year_id.'</td><td>'.$mba_semester_data->semester_number.'</td><td>'.$o_invoice->invoice_description.'</td><td>'.$o_invoice->invoice_id.'</td>';
                                // $table_list .= '</tr>';
                                // print($table_list);


                                print($i++.'. '.$o_invoice->personal_data_name.' '.$mba_student_data[0]->academic_year_id.' - '.$o_invoice->invoice_description);
                                // print('<pre>');var_dump($o_invoice_data);exit;
                                print('<br>');
                            }
                        }
                    }
                }
            }
            // print('</table>');
        }
    }

    public function create_invoice_open_payment()
    {
        show_403();exit;
        $a_billing_data = array(
            'billing_type' => 'o',
            'customer_name' => 'IULI Research',
            'virtual_account' => '8310504920221011',
            'description' => 'Research Virtual Account',
            'datetime_expired' => date('Y-m-d 23:59:59', strtotime('2022-12-31 23:59:59')),
            'customer_email' => 'bni.employee@company.ac.id'
        );

        // $a_billing_data = array(
        //     'billing_type' => 'o',
        //     'customer_name' => 'Sponsorship IULIFest',
        //     'virtual_account' => '8310884920220619',
        //     'description' => 'Sponsorship IULIFest',
        //     'datetime_expired' => date('Y-m-d 23:59:59', strtotime('2022-12-31 23:59:59')),
        //     'customer_email' => 'bni.employee@company.ac.id'
        // );

        $a_return_billing_data = $this->Bnim->create_billing($a_billing_data);
        print('<pre>');
        var_dump($a_return_billing_data);
    }

    public function invoice_report_list($s_academic_year_id, $s_semester_type_id)
    {
        // student_id yang lewat dr 15 semester
        $a_student_extend = [
            '2f757ae7-b8c1-4e10-a5df-9638a5ca2df1','8f96d53f-44c9-4d27-89f2-8c19ffb52c58','04b241e8-7f61-42dc-aca3-c26d2918294a','2d3451bb-17c3-4ad0-bf16-64693daeb78f','18f09580-07af-48f8-ad34-d3c9e8c4190d'
        ];
        // print('function disabled!');exit;
        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $mba_student_active = $this->Stm->get_student_filtered(['finance_year_id <=' => $s_academic_year_id], ['active', 'onleave','inactive']);

        $a_semester_id_null = [];

        if ($mba_student_active) {
            $s_file_name = 'TuitionFee_Billed_Academic_Year_'.$s_academic_year_id.'-'.$s_semester_type_id.'_Semester1-8';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Tuition Fee Billed Semester 1-8");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Student Number");
            $o_sheet->setCellValue('C1', "Batch");
            $o_sheet->setCellValue('D1', "Year In");
            $o_sheet->setCellValue('E1', "Program");
            $o_sheet->setCellValue('F1', "Faculty");
            $o_sheet->setCellValue('G1', "Study Program");
            $o_sheet->setCellValue('H1', "Semester");
            // $o_sheet->setCellValue('I1', "Semester Setting");
            $o_sheet->setCellValue('J1', "Semester Leave");
            $o_sheet->setCellValue('K1', "Scholarship");
            $o_sheet->setCellValue('L1', "Siblings");
            $o_sheet->setCellValue('M1', "Billed Semester");
            $o_sheet->setCellValue('N1', "Tuition Fee");
            $o_sheet->setCellValue('O1', "Billed Amount");

            $i_row = 1;

            foreach ($mba_student_active as $o_student) {
                if (!in_array($o_student->student_id, $a_student_extend)) {
                    if ($o_student->student_mark_completed_defense != 1) {
                        $mba_student_score_semester = $this->Scm->get_historycal_score($o_student->student_id, [1, 2]);
                        $i_row++;
                        $s_semester_number = 0;
                        if ($mba_student_score_semester) {
                            foreach ($mba_student_score_semester as $o_score) {
                                if (!is_null($o_score->semester_id)) {
                                    $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_score->semester_id])[0];
                                    if ($mbo_semester_data->semester_number > $s_semester_number) {
                                        $s_semester_number = $mbo_semester_data->semester_number;
                                    }
                                }else{
                                    print('semester id is null for student_id '.$o_student->student_id.' academic year '.$o_score->semester_academic_year_id);exit;
                                }
                            }
                        }
    
                        $s_semester_number++;
                        // $s_semester_id_setting = modules::run('krs/parsing_semester_type', $o_student->student_id, $s_academic_year_id, $s_semester_type_id);
                        // if ((is_null($s_semester_id_setting)) OR ($s_semester_id_setting == '')) {
                        //     print('student not found in semester_setting: ');
                        //     print($o_student->student_id);exit;
                        // }
                        $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $s_semester_number])[0];
                        // $mbo_semester_data_setting = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id_setting])[0];
                        
                        $a_data = [
                            'personal_data_name' => $o_student->personal_data_name,
                            'student_number' => $o_student->student_number,
                            'batch' => $o_student->academic_year_id,
                            'year_id' => $o_student->finance_year_id,
                            'program' => $o_student->program_name,
                            'faculty' => $o_student->faculty_abbreviation,
                            'study_program' => $o_student->study_program_name,
                            'semester_id' => $mbo_semester_data->semester_id,
                            // 'semester_setting_id' => $s_semester_id_setting
                        ];
    
                        $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                        $o_sheet->setCellValue('B'.$i_row, $o_student->student_number);
                        $o_sheet->setCellValue('C'.$i_row, $o_student->academic_year_id);
                        $o_sheet->setCellValue('D'.$i_row, $o_student->finance_year_id);
                        $o_sheet->setCellValue('E'.$i_row, $o_student->program_name);
                        $o_sheet->setCellValue('F'.$i_row, $o_student->faculty_abbreviation);
                        $o_sheet->setCellValue('G'.$i_row, $o_student->study_program_abbreviation);
                        $o_sheet->setCellValue('H'.$i_row, $mbo_semester_data->semester_number);
                        // $o_sheet->setCellValue('H'.$i_row, $mbo_semester_data_setting->semester_number);
    
                        // Semester Leave
                        // $mba_student_semester = $this->Smm->get_semester_student($o_student->student_id, ['ss.student_semester_status != ' => 'active'], [1,2]);
                        $mba_student_semester = $this->Smm->get_semester_student($o_student->student_id, ['ss.student_semester_status' => 'onleave'], [1,2]);
                        $a_semester_id_leave = [];
                        $a_semester_number_leave = [];
                        if ($mba_student_semester) {
                            foreach ($mba_student_semester as $o_student_semester) {
                                $mbo_semester_data_leave = $this->General->get_where('ref_semester', ['semester_id' => $o_student_semester->semester_id])[0];
                                if (!$mbo_semester_data_leave) {
                                    if (!in_array($o_student->student_id, $a_semester_id_null)) {
                                        array_push($a_semester_id_null, $o_student->student_id);
                                    }
    
                                    // print('semester data is null<pre>');
                                    // var_dump($o_student_semester);exit;
                                }
                                else if (!in_array($o_student_semester->semester_id, $a_semester_id_leave)) {
                                    array_push($a_semester_id_leave, $o_student_semester->semester_id);
                                    array_push($a_semester_number_leave, $mbo_semester_data_leave->semester_number);
                                }
                            }
                        }
                        $a_data['semester_id_leave'] = implode(',', $a_semester_id_leave);
                        $o_sheet->setCellValue('J'.$i_row, implode(', ', $a_semester_number_leave));
    
                        if (count($a_semester_id_leave) == 0) {
                            $o_sheet->setCellValue('M'.$i_row, $mbo_semester_data->semester_number);
                        }
    
                        if ($s_semester_number <= 8) {
                            // Scholarship
                            $mba_scholarship_data = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $o_student->personal_data_id]);
                            $a_scholarship_id = [];
                            $a_scholarship_name = [];
                            if ($mba_scholarship_data) {
                                foreach ($mba_scholarship_data as $o_ps) {
                                    $mbo_scholarship_data = $this->General->get_where('ref_scholarship', ['scholarship_id' => $o_ps->scholarship_id])[0];
                                    // if ($mbo_scholarship_data->scholarship_fee_type == 'main') {
                                    //     $d_fee_schoolarship += $o_ps->semester_fee;
                                    // }
                                    
                                    if (!in_array($o_ps->scholarship_id, $a_scholarship_id)) {
                                        array_push($a_scholarship_id, $o_ps->scholarship_id);
                                        array_push($a_scholarship_name, $mbo_scholarship_data->scholarship_name);
                                    }
                                }
                            }
                            $a_data['scholaship_id'] = implode(',', $a_scholarship_id);
                            $o_sheet->setCellValue('K'.$i_row, implode(', ', $a_scholarship_name));
    
                            // Siblings
                            $a_siblings_personal_data_id = [];
                            $a_siblings_personal_data_name = [];
                            // if ($o_student->personal_data_mother_maiden_name != '') {
                            //     $mba_personal_data_siblings = $this->General->get_where('dt_personal_data', ['personal_data_mother_maiden_name' => $o_student->personal_data_mother_maiden_name]);
                            //     if (count($mba_personal_data_siblings) > 1) {
                            //         foreach ($mba_personal_data_siblings as $o_personal_data) {
                            //             if ($o_personal_data->personal_data_id != $o_student->personal_data_id) {
                            //                 if (!in_array($o_personal_data->personal_data_id, $a_siblings_personal_data_id)) {
                            //                     array_push($a_siblings_personal_data_id, $o_personal_data->personal_data_id);
                            //                     array_push($a_siblings_personal_data_name, $o_personal_data->personal_data_name);
                            //                 }
                            //             }
                            //         }
                            //     }
                            // }
                            // $a_data['siblings_personal_data_id'] = implode(',', $a_siblings_personal_data_id);
                            // $o_sheet->setCellValue('K'.$i_row, implode(', ', $a_siblings_personal_data_name));
    
                            // // Billed Semester
                            // $s_billed_semester_id = $mbo_semester_data->semester_id;
                            // if (count($a_semester_id_leave) > 0) {
                            //     $a_even_semester_id = [];
                            //     if ($s_billed_semester_id <= 8) {
                            //         foreach ($a_semester_id_leave as $s_semester_id) {
                            //             # code...
                            //         }
                            //     }
                            // }
                            // $a_data['billed_semester_id'] = $s_billed_semester_id;
    
                            // Tuition Fee
                            // $mbo_fee_semester = $this->Im->get_fee([
                            //     'payment_type_code' => '02', 
                            //     'program_id' => $o_student->student_program,
                            //     'study_program_id' => $o_student->study_program_id,
                            //     'academic_year_id' => $o_student->finance_year_id,
                            //     'fee_ampunt_type' => 'main',
                            //     'semester_id' => 
                            // ]);
    
                            $mbo_invoice_last = $this->General->get_last_invoice($o_student->personal_data_id);
                            if ($mbo_invoice_last) {
                                $mbo_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $mbo_invoice_last->invoice_id])[0];
    
                                $a_data['tuition_fee'] = $mbo_sub_invoice->sub_invoice_amount;
                                $o_sheet->setCellValue('N'.$i_row, $mbo_sub_invoice->sub_invoice_amount);
                            }else{
                                $a_data['tuition_fee'] = 0;
                            }
                        }
                    }
                }
            }

            if (count($a_semester_id_null) > 0) {
                print('<pre>');
                var_dump($a_semester_id_null);exit;
            }

            $c_col = 'A';
            for ($i = 1; $i < 15; $i++) { 
                $o_sheet->getColumnDimension($c_col++)->setAutoSize(true);
            }
            // $o_sheet->getStyle('A1:N'.($i_row-1))->applyFromArray($style_border);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
        }
    }

    public function list_invoice()
    {
        // student_id yang lewat dr 15 semester
        $a_student_extend = [
            '2f757ae7-b8c1-4e10-a5df-9638a5ca2df1','8f96d53f-44c9-4d27-89f2-8c19ffb52c58','04b241e8-7f61-42dc-aca3-c26d2918294a','2d3451bb-17c3-4ad0-bf16-64693daeb78f','18f09580-07af-48f8-ad34-d3c9e8c4190d'
        ];
        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_academic_year_id = 2022;
        $s_semester_type_id = 1;

        $mba_student_active = $this->Stm->get_student_filtered(['finance_year_id <=' => 2021], ['active','inactive','onleave']);
        $print_data = [];

        if ($mba_student_active) {
            $s_file_name = 'TuitionFee_Academic_Year_'.$s_academic_year_id.'-'.$s_semester_type_id.'_Semester1-8';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Tuition Fee Semester 1-8");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Student Number");
            $o_sheet->setCellValue('C1', "Batch");
            $o_sheet->setCellValue('D1', "Year In");
            $o_sheet->setCellValue('E1', "Faculty");
            $o_sheet->setCellValue('F1', "Study Program");
            $o_sheet->setCellValue('G1', "Semester");
            $o_sheet->setCellValue('H1', "Semester Setting");
            $o_sheet->setCellValue('I1', "Semester Leave");
            $o_sheet->setCellValue('J1', "Scholarship");
            $o_sheet->setCellValue('K1', "Siblings");
            $o_sheet->setCellValue('L1', "Billed Semester");
            $o_sheet->setCellValue('M1', "Tuition Fee");
            $o_sheet->setCellValue('N1', "Billed Amount");

            $i_row = 1;

            foreach ($mba_student_active as $o_student) {
                if ($o_student->student_mark_completed_defense != 1) {
                    $i_row++;
                    // Next Semester
                    $mba_student_score_semester = $this->Scm->get_historycal_score($o_student->student_id, [1, 2]);
                    $s_semester_number = 1;
                    if ($mba_student_score_semester) {
                        foreach ($mba_student_score_semester as $o_score) {
                            if (!is_null($o_score->semester_id)) {
                                $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_score->semester_id])[0];
                                if ($mbo_semester_data->semester_number > $s_semester_number) {
                                    $s_semester_number = $mbo_semester_data->semester_number;
                                }
                            }else{
                                print('semester id is null for student_id '.$o_student->student_id.' academic year '.$o_score->semester_academic_year_id);exit;
                            }
                        }
                    }

                    $s_semester_number++;
                    // $s_semester_id_setting = modules::run('krs/parsing_semester_type', $o_student->student_id, $s_academic_year_id, $s_semester_type_id);
                    // if ((is_null($s_semester_id_setting)) OR ($s_semester_id_setting == '')) {
                    //     print('student not found in semester_setting: ');
                    //     print($o_student->student_id);exit;
                    // }
                    $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $s_semester_number])[0];
                    // $mbo_semester_data_setting = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id_setting])[0];
                    
                    $a_data = [
                        'personal_data_name' => $o_student->personal_data_name,
                        'student_number' => $o_student->student_number,
                        'batch' => $o_student->academic_year_id,
                        'year_id' => $o_student->finance_year_id,
                        'faculty' => $o_student->faculty_abbreviation,
                        'study_program' => $o_student->study_program_abbreviation,
                        'semester_id' => $mbo_semester_data->semester_id,
                        // 'semester_setting_id' => $s_semester_id_setting
                    ];

                    $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                    $o_sheet->setCellValue('B'.$i_row, $o_student->student_number);
                    $o_sheet->setCellValue('C'.$i_row, $o_student->academic_year_id);
                    $o_sheet->setCellValue('D'.$i_row, $o_student->finance_year_id);
                    $o_sheet->setCellValue('E'.$i_row, $o_student->faculty_abbreviation);
                    $o_sheet->setCellValue('F'.$i_row, $o_student->study_program_abbreviation);
                    $o_sheet->setCellValue('G'.$i_row, $mbo_semester_data->semester_number);
                    // $o_sheet->setCellValue('H'.$i_row, $mbo_semester_data_setting->semester_number);

                    // Semester Leave
                    $mba_student_semester = $this->Smm->get_semester_student($o_student->student_id, ['ss.student_semester_status' => 'onleave'], [1,2]);
                    $a_semester_id_leave = [];
                    $a_semester_number_leave = [];
                    if ($mba_student_semester) {
                        foreach ($mba_student_semester as $o_student_semester) {
                            $mbo_semester_data_leave = $this->General->get_where('ref_semester', ['semester_id' => $o_student_semester->semester_id])[0];
                            if (!in_array($o_student_semester->semester_id, $a_semester_id_leave)) {
                                array_push($a_semester_id_leave, $o_student_semester->semester_id);
                                array_push($a_semester_number_leave, $mbo_semester_data_leave->semester_number);
                            }
                        }
                    }
                    $a_data['semester_id_leave'] = implode(',', $a_semester_id_leave);
                    $o_sheet->setCellValue('I'.$i_row, implode(', ', $a_semester_number_leave));

                    if (count($a_semester_id_leave) == 0) {
                        $o_sheet->setCellValue('L'.$i_row, $mbo_semester_data->semester_number);
                    }

                    if ($s_semester_number <= 8) {
                        // Scholarship
                        $mba_scholarship_data = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $o_student->personal_data_id]);
                        $a_scholarship_id = [];
                        $a_scholarship_name = [];
                        if ($mba_scholarship_data) {
                            foreach ($mba_scholarship_data as $o_ps) {
                                $mbo_scholarship_data = $this->General->get_where('ref_scholarship', ['scholarship_id' => $o_ps->scholarship_id])[0];
                                // if ($mbo_scholarship_data->scholarship_fee_type == 'main') {
                                //     $d_fee_schoolarship += $o_ps->semester_fee;
                                // }
                                
                                if (!in_array($o_ps->scholarship_id, $a_scholarship_id)) {
                                    array_push($a_scholarship_id, $o_ps->scholarship_id);
                                    array_push($a_scholarship_name, $mbo_scholarship_data->scholarship_name);
                                }
                            }
                        }
                        $a_data['scholaship_id'] = implode(',', $a_scholarship_id);
                        $o_sheet->setCellValue('J'.$i_row, implode(', ', $a_scholarship_name));

                        // Siblings
                        $mba_sibling_data = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $o_student->personal_data_id, 'personal_data_id_sibling_with != ' => NULL]);
                        if ($mba_sibling_data) {
                            $mba_sibling_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mba_sibling_data[0]->personal_data_id_sibling_with]);
                        }
                        // $a_siblings_personal_data_id = [];
                        // $a_siblings_personal_data_name = [];
                        // if ($o_student->personal_data_mother_maiden_name != '') {
                        //     $mba_personal_data_siblings = $this->General->get_where('dt_personal_data', ['personal_data_mother_maiden_name' => $o_student->personal_data_mother_maiden_name]);
                        //     if (count($mba_personal_data_siblings) > 1) {
                        //         foreach ($mba_personal_data_siblings as $o_personal_data) {
                        //             if ($o_personal_data->personal_data_id != $o_student->personal_data_id) {
                        //                 if (!in_array($o_personal_data->personal_data_id, $a_siblings_personal_data_id)) {
                        //                     array_push($a_siblings_personal_data_id, $o_personal_data->personal_data_id);
                        //                     array_push($a_siblings_personal_data_name, $o_personal_data->personal_data_name);
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }
                        // $a_data['siblings_personal_data_id'] = implode(',', $a_siblings_personal_data_id);
                        // $o_sheet->setCellValue('K'.$i_row, implode(', ', $a_siblings_personal_data_name));
                        $a_data['siblings_personal_data_id'] = ($mba_sibling_data) ? $mba_sibling_data[0]->personal_data_id_sibling_with : '';
                        $o_sheet->setCellValue('K'.$i_row, (($mba_sibling_data) ? $mba_sibling_personal_data[0]->personal_data_name : ''));

                        // // Billed Semester
                        // $s_billed_semester_id = $mbo_semester_data->semester_id;
                        // if (count($a_semester_id_leave) > 0) {
                        //     $a_even_semester_id = [];
                        //     if ($s_billed_semester_id <= 8) {
                        //         foreach ($a_semester_id_leave as $s_semester_id) {
                        //             # code...
                        //         }
                        //     }
                        // }
                        // $a_data['billed_semester_id'] = $s_billed_semester_id;

                        // Tuition Fee
                        $a_fee_filter = [
                            'payment_type_code' => '02',
                            'program_id' => $o_student->program_id,
                            'study_program_id' => $o_student->study_program_id,
                            'academic_year_id' => $o_student->finance_year_id,
                            'semester_id' => $mbo_semester_data->semester_id,
                            'fee_amount_type' => 'main',
                            'scholarship_id' => null
                        ];
                        $mbo_fee_data = $this->Im->get_fee($a_fee_filter)[0];

                        if (!$mbo_fee_data) {
                            print('fee for: '.$o_student->personal_data_name.' student_id: '.$o_student->student_id);
                            var_dump($a_fee_filter);exit;
                        }
                        $a_data['tuition_fee'] = $mbo_fee_data->fee_amount;
                        $o_sheet->setCellValue('M'.$i_row, $mbo_fee_data->fee_amount);

                        // Billed Amount
                        $d_amount_billed = $mbo_fee_data->fee_amount;
                        if (count($a_scholarship_id) > 0) {
                            foreach ($a_scholarship_id as $s_scholarship_id) {
                                $mbo_scholarship_data = $this->General->get_where('ref_scholarship', ['scholarship_id' => $s_scholarship_id])[0];
                                if ($mbo_scholarship_data->scholarship_fee_type == 'main') {
                                    $a_fee_filter = [
                                        'program_id' => $o_student->program_id,
                                        'payment_type_code' => '02',
                                        'scholarship_id' => $s_scholarship_id,
                                        'study_program_id' => $o_student->study_program_id,
                                        'academic_year_id' => $o_student->finance_year_id,
                                        'semester_id' => $mbo_semester_data->semester_id,
                                        'fee_amount_type' => 'main'
                                    ];
                                    $mbo_fee_data = $this->Im->get_fee($a_fee_filter)[0];
                
                                    if (!$mbo_fee_data) {
                                        print('fee scholarship additional for: '.$o_student->personal_data_name.' student_id: '.$o_student->student_id);
                                        var_dump($a_fee_filter);exit;
                                    }

                                    $mbo_personal_data_scholarship = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $o_student->personal_data_id, 'scholarship_id' => $s_scholarship_id])[0];
                                    $d_amount_billed = $mbo_personal_data_scholarship->semester_fee;
                                }
                            }

                            foreach ($a_scholarship_id as $s_scholarship_id) {
                                $mbo_scholarship_data = $this->General->get_where('ref_scholarship', ['scholarship_id' => $s_scholarship_id])[0];
                                if ($mbo_scholarship_data->scholarship_fee_type != 'main') {
                                    $a_fee_filter = [
                                        'program_id' => $o_student->program_id,
                                        'scholarship_id' => $s_scholarship_id,
                                        'academic_year_id' => $o_student->finance_year_id,
                                        'fee_amount_type' => 'additional'
                                    ];
                                    $mbo_fee_data = $this->Im->get_fee($a_fee_filter)[0];
                
                                    if (!$mbo_fee_data) {
                                        print('fee scholarship additional for: '.$o_student->personal_data_name.' student_id: '.$o_student->student_id);
                                        var_dump($a_fee_filter);exit;
                                    }

                                    if ($mbo_fee_data->fee_amount_sign_type == 'negative') {
                                        if ($mbo_fee_data->fee_amount_number_type == 'percentage') {
                                            $d_amount_scholarship = $d_amount_billed * $mbo_fee_data->fee_amount / 100;
                                            $d_amount_billed = $d_amount_billed - $d_amount_scholarship;
                                        }else{
                                            $d_amount_billed = $d_amount_billed - $mbo_fee_data->fee_amount;
                                        }
                                    }
                                }
                            }
                        }
                        $a_data['amount_billed'] = $d_amount_billed;
                        $o_sheet->setCellValue('N'.$i_row, $d_amount_billed);

                        array_push($print_data, $a_data);
                    }
                }
            }

            // $style_border = array(
            //     'borders' => array(
            //         'allBorders' => array(
            //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            //             'color' => ['argb' => '00000000'],
            //         )
            //     )
            // );

            $c_col = 'A';
            for ($i = 1; $i < 15; $i++) { 
                $o_sheet->getColumnDimension($c_col++)->setAutoSize(true);
            }
            // $o_sheet->getStyle('A1:N'.($i_row-1))->applyFromArray($style_border);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;

            $print_data = array_values($print_data);
        }
        print('<pre>');
        var_dump($print_data);
        exit;
    }

    public function cheat_student_semester()
    {
        show_404();exit;
        $a_student_id = [
            '1a4e6895-9a44-449f-9005-8953bfa44cfb','db11cedd-c2e3-47c0-b841-1b9010885fd7','f116de58-a09e-48b9-9816-8e4776710cfe','22de4001-7945-41df-a3f5-5e2d480311c4',
            'ded1b211-608a-4fbd-8a30-44be1d3d5bab','976aa31d-b66c-4f00-bd90-8b647b9614d0','8dfba19d-2a71-4b75-8017-821f1b9569bf','9b17a567-dc22-497e-822a-f947b7fbbfb5',
            '17e00a92-8566-490e-b87c-3c48bf951fb0','ce5e90e9-c5ab-4f21-8b1b-efe1babbbb6f','cb5b759e-67e8-4720-b540-1ebf871673f1','210c71a9-337d-49be-a8bb-1a01fa9a4f39',
            '29fb8588-3bf9-44cc-b781-1b629e43f2a1','3dbbb377-99f5-4db7-8c39-df6189e85992','82527b11-065e-4d33-af65-95a09a2f6209','ad47b529-49e8-4864-a708-6ba18b8bfa3c',
            '10dc089e-d5cf-4857-8e54-b3e7e4908b69','1ff2f2a4-b4e0-4625-bd2d-cd998aff56bc','004ddb6b-2014-4c77-bbd8-b75d38d82770','e1734086-3a77-4c88-847e-c3392b93f7da',
            'd7311f9e-45fc-4f58-97a9-c2409fc08226','5af4ea6f-c480-4a1a-9c7b-20e418fd34a4','046fefe4-098b-4eab-81cb-0a18decd5b1a','81189cca-dfff-470d-ab83-27749bcfe54a',
            'f8589638-dd8e-4a9c-9ed5-1782437cc5cf','777ae37a-8371-492b-8219-52988cc04ac3','516dfaa8-4e07-4a30-957d-3bd6826b1832','7912ef6e-b740-4236-85c9-4a7fa066a9e6',
            '1cb4962d-a6b4-4447-bf90-c946862d9d3d','7aa539f2-2b89-4501-826f-2d3e9dc9f8f5','1f31be6f-73e0-4d4b-8451-d5f6bf7e9661','70420b44-b7ea-47f8-ad8f-a28f7e14f40b',
            'e5f60575-aa1c-427f-9bf2-8dc8526b310d','518a51a6-7326-45b5-aa11-0bcb0bde7f7b','d02e3672-14e7-4b23-973f-ed3323e5c659','20ce5784-565a-4ab8-b1d1-6d1892c496db',
            '2bb67365-b6ab-423c-988a-548c991266af','a0c06620-0d22-4b06-bee3-14a15e57337c','18896906-63e5-4c80-ae87-82af7c5fc998','6975bc62-9dad-4c9a-8bc1-3c023db90caa',
            '8726754a-c6d6-4739-aa64-479ffcfb7b20','964a1237-2202-48a5-bfcb-ecd6cd42b73e','98ffd328-21e0-41a0-8229-828dcf1f546c','cefd7eac-8462-49be-8f9c-f2c7d5e062fb',
            'f4f0ffd7-e0d9-47c1-b40c-575c4d4d361a','01eb24b9-4979-4ba4-bfca-06e2e0c7fb1d','214ed020-7b01-4ed9-be50-3acb617279d0','308b26f1-017b-47a6-b668-57d51a0078d4',
            '982d19b6-36e8-4e05-b67c-d42febbed808','e477aef8-b23b-4e2d-a076-ea4f10b01acb','4cea2049-4b45-4ba4-aca2-34c46cb3519b','6d83fa5b-5899-41db-a8b9-07519693b8a8',
            '19ce40d0-9b61-4d4a-97ab-2d312ea5b45d','1c99a0ed-8220-4b92-a04b-5df72ba2935d','33a6e4ad-cf7c-4b05-96be-105844941710','5e030baf-549f-4213-8b79-b2ed8c6cedea',
            '718c0df2-5f8a-4031-ad31-d08286528b42','87f68919-5175-4edb-8f0a-c43103651af0','8d16d280-84c5-487c-abf3-99f76e4d7f08','ff0c8e5b-e8c8-47fd-b70e-e69ce830db10',
            '347ba252-c718-4134-af26-0792e65a7aaa','450bcb67-5e90-4158-b48e-ffed93413715','669816bc-f4da-486e-b194-cc338d09b0f4','6d2ccc39-51d1-4991-add4-ee215130b5b3',
            'e22df690-460c-4671-ab89-497103479dc0','01e14f1d-9b5c-4638-8f30-1b875f49ef48','620f63c1-2dfd-452b-8849-15de5f1920dd','fbb23301-056d-4147-9bd2-df7f0059cbf6',
            '9182ddfa-a209-4d8b-9194-698fb988426f','b88c44a8-795f-4e90-bac2-af5f86371376','21f96a35-e0fe-4c09-a429-7db303fff9b1','03253eda-1c2d-4e1a-9f72-e38c43ff20ed',
            '0de18a1c-c767-412b-8261-df99e603fcfd','cb59c9de-9c73-4908-99a4-c04c138ca848','302309de-5116-4fde-bd69-475464fdc83b','52912ca3-6146-407c-8c31-e5d89faaed44',
            'a6d2b44d-b85b-43bd-bd50-09ea74fa0e2f','b50bffd5-9cce-4907-93cd-818f8460501c','c15c9124-4e0b-43f7-92a0-180e4c785270','2d3f4bc3-2f5b-4b98-b9a8-248aa9df15e8',
            '3ae1d2fb-bf16-4a64-aae2-5835999278d5','4145f453-6fdc-4eb4-a4c0-6e50112fb04a','d84df1fc-6665-4ea6-a606-aad74a6c832e','eee72b3f-d464-4adc-9326-dfe9d85e2b23',
            'e035524c-0a3c-438d-bf6f-97e6e900011e','f8e068fa-ac16-45dc-bf5c-9aad42712500','0b542548-591d-4b61-9fb5-79b426cac913','36a18b49-fee8-4af9-9b38-95f32d75ea5e',
            '3b176623-2373-4e6c-a4a2-3f713ac28fa1','4eed3289-b288-4464-9b7f-a679b155cbe6','573f8e07-583b-4e47-8713-69cdd317c746','6d21f8c4-4b3a-48cd-9c0d-da36d18a819c',
            '8666f730-fbd7-4b53-b578-eb646347d175','d48e48bc-ac6b-4e45-9763-6e441bf4f86d','db41bb84-b3a1-43dc-b31b-b562ce765e2a','ecffad0a-40e7-4ad8-843f-a2858ac89123',
            '3c9de701-8a4d-46d7-859d-fd9ff9e60426','b43a5844-89c6-4101-ba31-b405bbcd10e5','cbea1cc4-58de-4713-8468-9cc75a5c44b7','d92907d0-adc6-45e3-8bf6-dc1e37c13966',
            'e893d8f4-ae8a-492b-98ce-1fbe330e7da8','f0f47870-f163-43f1-b7b9-72012bb23b5a','1218df3e-92ec-4d65-b264-3e7fa4b0ac0d','83ae2afd-6bc0-47a7-a8b2-253d9daea159',
            'c2f27713-a009-4cd9-a89b-96f9a1e1f603','f77ab29e-b265-4889-90fa-3695fe30e366','3e772464-8ab4-4b6b-a0fc-7fac7e33cc20','40bd6b02-c3eb-46d2-a3ac-884c1cd62169',
            '2a6c8ea8-34ca-43aa-8967-6bd237ca2f55','89fba670-eff3-44ab-86fa-2f6cd2c394c3','8ffa3497-8ba7-41fb-bbec-517e7fa3fe27','9da49fb7-5f15-480f-8722-6577a20cc0a1',
            'c07ee362-ca65-4f21-8874-025882786c1d','e4f31647-7082-430d-909f-99346aa6ae1f','300472cb-118b-4bdb-b8bd-d6af5952114e','923a796e-e1db-4c30-bbb0-1f917d9df299',
            '934dbd55-72be-4007-8962-ee9d7d466b7b','3fb17179-4d5b-4734-87ae-f171a85212e5','9ed88b0b-a31c-461d-992c-a0a9bceea6ae','ff13416c-0ec5-4115-8a7d-acaf6e99b183',
            '08faee47-08f4-4757-93a6-a92d934d2617','17ecfc4d-6d9e-4b7a-bc24-bda7e9719a29','46e85b83-7ce6-4102-9474-38a5e7a49939','477e3295-4af8-4865-a73d-0b5730820253',
            '4e6a0243-f50c-463f-a654-3035f1acc51b','87144876-f989-427d-a746-e572fa2e4593','9a9f58fd-4cd9-41da-a2e8-ecfdfe48b2a5','b0ed1090-0e38-4fa1-840c-f0f542451856',
            'b18b5ea8-508a-4df9-8d40-e9738dad82e0','b5a99742-b5f6-47d9-abca-1572d1ded31d','ea881d6d-21be-4858-bdfd-3dd30ed44eda','eb30a213-0b21-4834-84c8-0e681dc2d4d2',
            '9214ebd3-8612-4e87-b663-32d964d22673','033b159d-482f-453b-8862-4f1958e348c8','5bf29503-df84-4c7f-b187-88f88be0b20c','5d6b1408-4af7-4601-bb97-10688f89857b',
            '7a2b70bb-93f4-48d8-b437-5c477053b613','d5d2c193-3b48-4e94-a287-f4515a4af506','e15bdbf1-548d-42d7-b13f-bf891eb67311','92201f16-a926-4ae7-a99c-4696d0e63e2a',
            '9a834caa-e22d-4f62-8b88-adf910aa6126','f4b3ff04-ca18-4860-bae7-de3caa9f6396','b3c18c5a-89f9-4450-b355-16bb9acfdd13','9b8763ad-7966-4146-a220-46954e942e0b',
            'ed14d231-164b-4867-8452-6ba98ada422d','51b5239f-24a9-4274-86d1-9bed70a35cf9','3905377d-210e-4c1c-97b5-825b5572be5d','281ef202-301b-4b74-a484-0f4e03df44ff',
            '4587fb04-3925-47e2-8477-4d4d6b9d4eae','df2d1a19-df74-456d-9647-29d30ea7aa11','3fe6d2a4-1772-44ae-aca9-8a54b2aeeaa4','69fd2fc8-05df-4de0-ba9c-21c7b66f1492',
            '8a44da91-26a5-4581-942f-32c455271b06','49c3e3c5-7480-40d3-a4d1-e064fa78cd8d','c1801957-f93d-4dbe-9c7e-96c698cecee0','f82e85ae-ce4d-4b02-8376-301e9c2773f0',
            '059de62c-6bdf-4796-a99f-efb55c91808a','48b624fa-e719-401b-b55d-297a695d5041','fd881b07-550e-4eef-9c38-78df4b4f22d6','21161f02-a002-4db6-a798-e814358087db',
            '4b35e1bb-7d9f-47d0-8486-b5ddf8052c7c','4f35127b-7897-4ce4-b894-b3bc4887491a','50c2c721-1568-4682-ac55-3c42b76f1113','9efca642-14c2-4880-9ef9-4b46eee9efc0',
            'b9d29774-0a06-41af-b347-de4acecc656f','d8ea40e0-1cc2-44eb-97f8-e19f0634e8f7','e3d39a47-c394-4935-b90b-1854d7d5347e','e46f8190-dbea-4f14-a286-931e1f4340e1',
            'e95d5eab-0641-46b9-b3e5-af28bbe80403','5595c167-17c1-4db1-8b4e-5c774ea23044','5fc52bc9-58c5-4071-a507-a934b862a6d4','1a5937e4-1371-45f9-9690-adf5446b1317',
            '356231a9-2d0e-4e4d-adf2-ccb685804919','5bca8866-e3ef-41b3-b24b-3ae629489fa7','9c0b0588-585c-48d0-9d24-e79c0eb0623a','d39554cf-0d3b-4df8-9dcc-f69e4f53f06e',
            'df283a81-948f-4815-adeb-b0ea34392468','f7b4c1c9-2c01-4929-826d-29a9ef62846d','fa41737f-b924-4e89-a5b1-f781ee946e9d','868d8eaa-6c5d-48a4-9ace-506054e4dcf2',
            '8d70dda6-4f0f-4755-88b4-655e8197e4d0','d2ad7994-df49-4660-8a95-230009fc5679','e90afb5c-6594-49eb-9740-119ba11e3a49','4ad066b6-98a5-47fa-9676-b440079a9b08',
            '797d48ab-a864-4510-bcc3-9975454d7602','c521af7f-cb19-4c55-ad92-2caa25e6e2e8','1228f7d0-2ed1-497e-982a-9ddf1c18b25c','55874803-4094-4568-b71a-4ab3cb640a27',
            '5c6a60ca-e488-4585-9654-43872e1d4462','6cd7a07e-cac2-4136-b43e-4526168631d8','1a2854cc-208b-41c2-9c73-0af6bd6659a9','95737444-93a7-481f-a016-e1862bd08d89',
            '72fa39b9-ed98-4f8d-8a42-3bddbfeb7447','8138476e-c083-48db-a5ae-e9fc0c664583','bf182adf-76bd-4e2e-bd83-de47619b40a9','cf35718b-8cc2-4d84-a6cc-29b866c8115c',
            '2afe8973-b5b8-4e92-90e5-fb28c6f8cb09','8e3ade91-939c-4ca7-b2ce-bb24d8c00456','d31a4a1b-fbdf-4c88-b6fb-a141ba00da32','059f7b1d-b1bc-4802-b8dd-a263eab7bdc5',
            '116284a9-6d67-469f-85c0-89ba4fa60a47','1be55b77-2503-47a1-87ec-91ef98ba523b','420ff310-cab8-4610-b7d1-2599d6eae3c0','60d8cbca-d1d7-435c-a67c-ead6f7767e44',
            '85037a5b-f25e-43bb-b5c8-3c7f85fa95da','df0bbf8f-d11a-427d-be0a-6d3eb328c759','9e6033db-b388-46a0-9da9-e972d14405e0','da66ed8e-412e-4bef-8953-7eab9e672532',
            'fea67766-d047-4499-a267-f078d8096794','26d22e4e-5b95-41dc-937d-9d506fc3cdd7','17ea3630-474c-4ae6-bfaf-ec6bdd5fe959','381cb8bd-7d67-4705-bbec-0bc4f66b014a',
            '5826dea1-9da2-454b-b586-82cb8761ef05','b92985b8-3b27-4ace-8a9a-7d3a59ac997b','549f9f9f-3735-4a7b-81d5-3611172c5391','8cba03dd-0c10-4587-b967-bc6df01039af',
            '882c5bf0-9301-47fb-a87d-20c2b0702904','096b54d3-3ae7-4175-ba2d-73dfe4385b40','e916b364-c22f-4138-a019-2779f27eec35','2ce485fd-b21c-4504-8d8e-d8220a67ba36',
            'a3797ec4-341f-4a05-a971-d7ec174e5bb7','c99c2948-e694-484e-a620-d68d05adb450','0ec98f17-ec9f-4259-90cb-e1a0e1187e9f','5bfb3427-f247-4a1f-9c37-070d4f171893',
            '699dc15b-e263-4e98-86b4-8fe941405b8f','fbe5bbc5-93fe-4e67-9101-611af1b857b8','e5b5de99-c2c0-4f67-bcd2-5b419b8df219','fc17be54-ae0f-4c7c-b29d-a73e6566f838',
            '4cac2de8-457f-479a-a507-535bca71f5c5','6bc9a8e9-ddce-45b6-ac98-434aae452b74','ee52159c-d852-4370-93d9-2f05029e9948'
        ];
        foreach ($a_student_id as $s_student_id) {
            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
            $a_filter = [
                'ss.semester_id != ' => NULL,
                'ss.academic_year_id' => 2022,
                'ss.semester_type_id' => 2
            ];
            $mba_student_semester_data = $this->Smm->get_semester_student($s_student_id, $a_filter);
            if (!$mba_student_semester_data) {
                print('soong:'.$s_student_id.'<pre>');var_dump($a_filter);
                exit;
                print('<br>');
            }
            else {
                $mbo_student_last_semester = $mba_student_semester_data[count($mba_student_semester_data)-1];
                $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $mbo_student_last_semester->semester_id])[0];
                $i_semester_number = $mbo_semester_data->semester_number;

                $i_semester_number++;
                $mbo_new_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $i_semester_number]);
                if (!$mbo_new_semester_data) {
                    print('<pre> out of the box:');var_dump($mbo_student_data->personal_data_id.' -> '.$mbo_student_data->personal_data_name.'<br>');
                    exit;
                }
                
                $mbo_new_semester_data = $mbo_new_semester_data[0];
                // $a_student_semester_data = [
                //     'student_id' => $s_student_id,
                //     'semester_type_id' => 1,
                //     'academic_year_id' => 2023,
                //     'semester_id' => $mbo_new_semester_data->semester_id,
                //     'date_added' => date('Y-m-d H:i:s')
                // ];

                // $this->Stm->save_student_semester($a_student_semester_data);
                print($mbo_student_data->finance_year_id.'-'.$mbo_student_data->personal_data_name.'-Semester '.$mbo_new_semester_data->semester_number.'<br>');
            }
        }
    }

    public function cheat_student_semester_id()
    {
        $a_student_semester_lewat = [];
        // $a_student_semester_lewat = [
        //     '92f228ee-875f-460e-a273-78a35164a266',
        //     '3898a159-e5b1-4aa7-9f56-fe755db53fc6',
        //     '971ec46b-09f1-4062-961c-c16283dadc26',
        //     '799b7498-1238-428d-94b0-4440f9f578c2',
        //     'a33a4ed2-0fea-4ac1-9eff-4e29f1148167'
        // ];

        $s_academic_year_id = 2024;
        $s_semester_type_id = 1;
        $mba_student_semester_list = $this->General->get_where('dt_student_semester', [
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);
        // print('<pre>');
        // var_dump($mba_student_semester_list);exit;

        if ($mba_student_semester_list) {
            foreach ($mba_student_semester_list as $o_student_semester) {
                if (is_null($o_student_semester->semester_id)) {
                    $mbo_student_data = $this->Stm->get_student_by_id($o_student_semester->student_id);
                    if (!in_array($mbo_student_data->personal_data_id, $a_student_semester_lewat)) {
                        $a_filter = [
                            'ss.semester_id != ' => NULL,
                            'ss.academic_year_id' => 2023,
                            'ss.semester_type_id' => 2,
                            // 'stu.finance_year_id < ' => $s_academic_year_id
                        ];
    
                        // if ($s_semester_type_id == 7) {
                        //     $a_filter['ss.academic_year_id'] = $s_academic_year_id;
                        //     $a_filter['ss.semester_type_id'] = 1;
                        // }else if ($s_semester_type_id == 8) {
                        //     $a_filter['ss.academic_year_id'] = $s_academic_year_id;
                        //     $a_filter['ss.semester_type_id'] = 2;
                        // }
    
                        $mba_student_semester_data = $this->Smm->get_semester_student($o_student_semester->student_id, $a_filter);
                        if ((!$mba_student_semester_data) AND ($mbo_student_data->finance_year_id < $s_academic_year_id)) {
                            print('soong:'.$o_student_semester->student_id.'<pre>');var_dump($a_filter);
                            exit;
                        }
                        else {
                            if ($mbo_student_data->finance_year_id < $s_academic_year_id) {
                                $mbo_student_last_semester = $mba_student_semester_data[count($mba_student_semester_data)-1];
                                $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $mbo_student_last_semester->semester_id])[0];
                                $i_semester_number = $mbo_semester_data->semester_number;
                            }
                            else {
                                $i_semester_number = 0;
                            }
        
                            if (in_array($s_semester_type_id, [7,8])) {
                                $i_semester_number = $i_semester_number + 0.5;
                            }else{
                                $i_semester_number++;
                            }
        
                            if ($i_semester_number < 15) {
                                $mbo_new_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $i_semester_number]);
                                if (!$mbo_new_semester_data) {
                                    print('<pre> out of the box:');var_dump($mbo_student_data->personal_data_id.' -> '.$mbo_student_data->personal_data_name.'<br>');
                                    exit;
                                }
                                
                                $mbo_new_semester_data = $mbo_new_semester_data[0];
                                $this->Stm->save_student_semester([
                                    'semester_id' => $mbo_new_semester_data->semester_id
                                ], [
                                    'student_id' => $o_student_semester->student_id,
                                    'academic_year_id' => $s_academic_year_id,
                                    'semester_type_id' => $s_semester_type_id
                                ]);
            
                                // $this->Scm->update_score_semester(
                                //     $mbo_new_semester_data->semester_id,
                                //     $o_student_semester->student_id,
                                //     $s_academic_year_id,
                                //     $s_semester_type_id
                                // );
            
                                print($mbo_student_data->finance_year_id.'-'.$mbo_student_data->personal_data_name.'-Semester '.$mbo_new_semester_data->semester_number.'<br>');
                            }
                        }
                    }
                }
            }
            // print('<pre>');
            // var_dump($mba_student_semester_list);exit;
        }
    }

    public function cheat_student_semester_status()
    {
        $mba_student_list = $this->Stm->get_student_list_data(false, ['inactive','active', 'onleave','graduated','resign']);

        if ($mba_student_list) {
            $i_count = 0;
            $o_active_semester = $this->Smm->get_active_semester();
            foreach ($mba_student_list as $o_student) {
                $mba_student_semester = $this->Smm->get_semester_student($o_student->student_id, [
                    'ss.academic_year_id' => $o_active_semester->academic_year_id,
                    'ss.semester_type_id' => $o_active_semester->semester_type_id
                ]);

                if (!$mba_student_semester) {
                    
                    // $a_data = [
                    //     'student_id' => $o_student->student_id,
                    //     'semester_type_id' => $o_active_semester->semester_type_id,
                    //     'academic_year_id' => $o_active_semester->academic_year_id,
                    //     'student_semester_status' => ($o_student->student_status == 'active') ? 'inactive' : $o_student->student_status
                    // ];

                    // $save_data = $this->Smm->save_student_semester($a_data);

                    // if ($save_data) {
                    //     print($o_student->personal_data_name.' ('.$o_student->study_program_abbreviation.'/'.$o_student->academic_year_id.')');
                    //     $i_count++;
                    // }else{
                    //     var_dump($a_data);
                    // }

                    var_dump($o_student->student_id);
                    print('<br>');
                }else{
                    $a_data_update = [
                        'student_semester_status' => $o_student->student_status
                    ];

                    $save_data = $this->Smm->save_student_semester($a_data_update, [
                        'student_id' => $o_student->student_id,
                        'academic_year_id' => $o_active_semester->academic_year_id,
                        'semester_type_id' => $o_active_semester->semester_type_id,
                    ]);

                    print($o_student->personal_data_name.'<br>');
                }
                $i_count++;
            }

            print('<h1>'.$i_count.'</h1>');
        }
    }

    function send_email_alumni() {
        $a_student_email = [];
        $s_body = <<<TEXT
<p>Dalam rangka meningkatkan kualitas pendidikan di IULI guna terciptanya alumni yang berkualitas, dimohon bantuannya kepada seluruh alumni yang sudah bekerja untuk mengisi data pekerjaan di portal alumni (portal.iuli.ac.id) atau bisa langsung memberikan link dibawah kepada supervisor masing-masing.</p>
<p><a href="https://survey.iuli.ac.id/">https://survey.iuli.ac.id/</a></p>
<br>
jika ada pertanyaan seputar quesioner atau kesulitan mengakses portal bisa menghubungi nomor berikut:<br>
0818-557-441 (bobby/IT)
<p><br></p>
terima kasih,<br>
salam sukses
TEXT;
        foreach ($a_student_email as $s_student_email) {
            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_email' => $s_student_email, 'ds.student_status' => 'graduated']);
            if ($mba_student_data) {
                $o_student = $mba_student_data[0];
                $s_html = '<p>Selamat Pagi '.$o_student->personal_data_name.'</p>';
                $s_html .= $s_body;

                $config['mailtype'] = 'html';
                $this->email->initialize($config);

                $this->email->from('employee@company.ac.id');
                $this->email->to($o_student->student_email);
                if (!is_null($o_student->personal_data_email)) {
                    $this->email->cc($o_student->personal_data_email);
                }
                $this->email->subject('Survey Alumni');
                $this->email->message($s_html);

                if(!$this->email->send()){
                    // $this->log_activity('Email did not sent');
                    // $this->log_activity('Error Message: '.$this->email->print_debugger());
                    var_dump($this->email->print_debugger());
                }
                else{
                    print($o_student->student_email);
                    print(' send');
                    print('<br>');
                    $this->email->clear(TRUE);
                }
                // exit;
            }
        }
    }

    public function send_bulk_email()
    {
        $s_finance_year_id = '2020';
        // $this->load->library('email');
        $this->load->helper('path');

        $path = set_realpath(APPPATH.'uploads/finance/20201/files/');
        $file_names = directory_map($path);
        // print('<pre>');
        // var_dump($file_names);

        $mba_student_list_data = $this->Stm->get_student_filtered(['finance_year_id' => $s_finance_year_id], ['active']);
        if ($mba_student_list_data) {
            // $a_student_name = array_map(function($mba_student_list_data)
            // {
            //     return $mba_student_list_data->student_email;
            // }, $mba_student_list_data);
            // asort($a_student_name);
            // print('<pre>');
            foreach ($mba_student_list_data as $o_student) {
                if (!in_array($o_student->student_email, ['ayman.anismatta386@stud.iuli.ac.id', 'andi.hayasmine@stud.iuli.ac.id'])) {
                
    //                 print($o_student->personal_data_name);
    //                 print('<br>');
                    $s_body = <<<TEXT
Dear Student,
The following is material from Finance & Accounting delivered during Orientation Week MABA 2020, on Monday, August 24, 2020
TEXT;
    //                 $this->email->from('employee@company.ac.id');
                    $s_subject = '[FINANCE] Material for VA Payment Procedures';
                    $config = $this->config->item('mail_config');
                    // $config['mailtype'] = 'html';
                    $this->email->initialize($config);

                    $this->email->from('employee@company.ac.id');
                    $this->email->to($o_student->student_email);
                    $this->email->subject($s_subject);
                    $this->email->message($s_body);
                    foreach($file_names as $file_name) {
                        $this->email->attach($path.$file_name);
                    }

                    if(!$this->email->send()){
                        // $this->log_activity('Email did not sent');
                        // $this->log_activity('Error Message: '.$this->email->print_debugger());
                        var_dump($this->email->print_debugger());
                    }
                    else{
                        print($o_student->student_email);
                        print(' send');
                        print('<br>');
                        $this->email->clear(TRUE);
                    }
                }
            }
            // var_dump($a_student_name);
        }
    }

    public function blast_mail()
    {
        print('function closed!');exit;
        $this->db->from('dt_student');
        $this->db->where_in('student_status', ['active', 'onleave']);
        $this->db->where_in('academic_year_id', ['2019', '2020']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $mba_student_data = $query->result();
            $s_body = <<<TEXT
Dear Student,
Please attend this event
TEXT;
            $s_subject = '[ACADEMIC] Hochschule Aalen Presentation';
            $s_path = APPPATH.'uploads/temp/UAS_Aalen_Event.jpeg';
            $config = $this->config->item('mail_config');
            // $config['mailtype'] = 'html';
            $this->email->initialize($config);
            
            foreach ($mba_student_data as $o_student) {
                $this->email->to($o_student->student_email);
                $this->email->from('employee@company.ac.id');
                $this->email->subject($s_subject);
                $this->email->message($s_body);
                $this->email->attach($s_path);

                if(!$this->email->send()){
                    var_dump($this->email->print_debugger());
                }
                else {
                    print('send to '.$o_student->student_email);
                }
                $this->email->clear(TRUE);
                print('<br>');
            }
        }
        else {
            print('kosong!');
        }
    }

    public function update_status_to_inactive()
    {
        $s_graduated_date = '2020-10-02';
        $s_graduated_year_id = '2020';

        $mba_student_active = $this->General->get_where('dt_student', [
            'student_status' => 'active'
        ]);

        $i_count = 0;
        foreach ($mba_student_active as $o_student) {
            if (($o_student->academic_year_id != '2021') OR ($o_student->finance_year_id != '2021')) {
                
                $mba_student_score = $this->General->get_where('dt_score', [
                    'student_id' => $o_student->student_id,
                    'score_approval' => 'approved',
                    'academic_year_id' => 2021,
                    'semester_type_id' => 1
                ]);

                if (!$mba_student_score) {
                    $mba_student_score_true = $this->General->get_where('dt_score', [
                        'student_id' => $o_student->student_id,
                        'academic_year_id' => 2021,
                        'semester_type_id' => 1
                    ]);

                    $mba_student_invoice = $this->Im->get_invoice_list_detail([
                        'di.personal_data_id' => $o_student->personal_data_id,
                        'fee.payment_type_code' => '02',
                        'di.invoice_status != ' => 'cancelled',
                        'di.academic_year_id' => 2021,
                        'di.semester_type_id' => 1
                    ]);

                    $b_has_paid_tuition_fee = false;
                    if ($mba_student_invoice) {
                        $mba_invoice_sub_details = $this->Im->get_invoice_data([
                            'di.invoice_id' => $mba_student_invoice[0]->invoice_id
                        ]);

                        if ($mba_invoice_sub_details) {
                            foreach ($mba_invoice_sub_details as $o_invoice_details) {
                                if ($o_invoice_details->sub_invoice_details_status == 'paid') {
                                    $b_has_paid_tuition_fee = true;
                                    break;
                                }
                            }
                        }
                    }
                    // if ($o_student->student_mark_completed_defense == 1) {
                    //     $a_data_update = [
                    //         'graduated_year_id' => $s_graduated_year_id,
                    //         'student_date_graduated' => $s_graduated_date,
                    //         'student_status' => 'graduated'
                    //     ];
                    // }else{
                    //     $a_data_update = [
                    //         'student_status' => 'inactive'
                    //     ];
                    // }

                    // $this->Stm->update_student_data($a_data_update, $o_student->student_id);

                    $i_count++;
                    print($o_student->student_email.';'.$o_student->academic_year_id.';'.(($mba_student_invoice) ? $mba_student_invoice[0]->invoice_status : '-').';'.(($mba_student_score_true) ? $mba_student_score_true[0]->score_approval : '-').';'.$o_student->student_id);
                    print('<br>');
                }

                
            }
        }

        print('<h1>'.$i_count.'</h1>');
        // print('<pre>');
        // var_dump($mba_student_active);
    }

    function show_menu() {
        $list_topbar = $this->a_page_data['top_bar'];
        print('<pre>');var_dump($list_topbar);exit;
    }

    function get_assessment_list_lecturer($s_academic_year_id, $s_semester_type_id) {
        // $a_custom_employee = ["7d63b00f-a29a-4eb5-9758-9f0757cbe982","1a6abebe-21b6-42a5-9937-ec553e164067","81a7fb11-1d28-4815-9612-7c5dfe696b9c","1e6547f8-d6ba-4356-b389-8663d78cf11a","683c0a5d-24c8-417b-be0d-948f418ce688","dcc4c64e-7dea-4468-aff5-32bdabd99a0e","32b66e3d-a8f8-444b-a05c-bdf2cb5e365c","877e7b14-9fe1-4ea0-84d4-9d9378ecde44","8f33f6c8-43b7-4e54-85c7-e795d38afc75","adc2bb27-e00d-4d4e-85fc-560cd8cfffed","861fc66f-e39a-4975-8009-1424508e97bc","6aa4f131-b15a-4f05-95c0-c3cb7a476900","59833f8c-7ba3-421b-a4ba-74d3aa8699da","06a3e262-8e57-46cd-92b0-69dc3ff0546e","944dfaf3-8790-4c2d-a779-827d517ce4a3","89edb30a-2eed-49b2-906c-edc0cba570a6","123b9806-fe93-49e0-9808-2cb2fb922479"];
        $a_custom_employee = false;
        $style_vertical_top = array(
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            )
        );
        $style_border = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                )
            )
        );
        $mba_lectlist = $this->Vm->get_lecturer_list_assessment([
            'd1cm.academic_year_id' => $s_academic_year_id,
            'd1cm.semester_type_id' => $s_semester_type_id,
        ], true, false, $a_custom_employee);

        if ($mba_lectlist) {
            // $question_aspect_list = $this->Vm->get_question_list([
            //     'question_status' => 'active'
            // ]);
            $question_aspect_list = $this->Vm->get_lecturer_assessment_result([
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'ar.result_id' => $mba_lectlist[0]->result_id
            ]);

            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Lecturer_Assessment_Result_'.$s_academic_year_id.$s_semester_type_id.'.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Academic Services")
                ->setCategory("Lecturer Assessment");
    
            $i_row = $i_startrow = 1;
            $o_sheet->setCellValue('A'.$i_row, 'No');
            $o_sheet->setCellValue('B'.$i_row, 'Lecturer');
            $o_sheet->setCellValue('C'.$i_row, 'Subject');
            $o_sheet->setCellValue('D'.$i_row, 'Study Program');
            $o_sheet->setCellValue('E'.$i_row, 'Count of Responden');
            $o_sheet->setCellValue('F'.$i_row, 'Total Score of Question Number');
            $i_row++;
            
            $c_col_question = $c_col = 'F';
            if ($question_aspect_list) {
                foreach ($question_aspect_list as $o_question) {
                    $o_sheet->setCellValue($c_col++.$i_row, $o_question->number);
                }
            }
            // print('<pre>');var_dump($c_col);exit;
            // $c_col--;
            $c_col = chr(ord($c_col) - 1);
            $o_sheet->mergeCells("F".($i_row - 1).":".$c_col++.($i_row - 1));

            $o_sheet->setCellValue($c_col.($i_row-1), 'Total Score');
            $o_sheet->mergeCells($c_col.($i_row - 1).':'.$c_col.$i_row);
            $o_sheet->getStyle($c_col.($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle($c_col++.($i_row - 1))->getAlignment()->setWrapText(true);

            $o_sheet->setCellValue($c_col.($i_row-1), 'Average Score');
            $o_sheet->mergeCells($c_col.($i_row - 1).':'.$c_col.$i_row);
            $o_sheet->getStyle($c_col.($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle($c_col++.($i_row - 1))->getAlignment()->setWrapText(true);

            $o_sheet->setCellValue($c_col.($i_row-1), 'Assessment Result');
            $o_sheet->mergeCells($c_col.($i_row - 1).':'.$c_col.$i_row);
            $o_sheet->getStyle($c_col.($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle($c_col++.($i_row - 1))->getAlignment()->setWrapText(true);

            // $c_col--;
            $c_col = chr(ord($c_col) - 1);
            $c_colend = $c_col;

            $o_sheet->mergeCells("A".($i_row - 1).":A".$i_row);
            $o_sheet->mergeCells("B".($i_row - 1).":B".$i_row);
            $o_sheet->mergeCells("C".($i_row - 1).":C".$i_row);
            $o_sheet->mergeCells("D".($i_row - 1).":D".$i_row);
            $o_sheet->mergeCells("E".($i_row - 1).":E".$i_row);

            $o_sheet->getStyle("A".($i_row - 1))->getAlignment()->setWrapText(true);
            $o_sheet->getStyle("B".($i_row - 1))->getAlignment()->setWrapText(true);
            $o_sheet->getStyle("C".($i_row - 1))->getAlignment()->setWrapText(true);
            $o_sheet->getStyle("D".($i_row - 1))->getAlignment()->setWrapText(true);
            $o_sheet->getStyle("E".($i_row - 1))->getAlignment()->setWrapText(true);

            $o_sheet->getStyle("A".($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle("B".($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle("C".($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle("D".($i_row - 1))->applyFromArray($style_vertical_top);
            $o_sheet->getStyle("E".($i_row - 1))->applyFromArray($style_vertical_top);

            $i_row++;

            $xnumber = 1;
            foreach ($mba_lectlist as $o_lecturer) {
                $mbo_class_study_prog = $this->Cgm->get_class_master_study_program($o_lecturer->class_master_id);
                $mba_assessment_result = $this->Vm->get_lecturer_score_counter([
                    'd1sc.class_master_id' => $o_lecturer->class_master_id,
                    'd2ar.employee_id' => $o_lecturer->employee_id
                ]);
                $a_class_study_prog = [];
                if ($mbo_class_study_prog) {
                    foreach ($mbo_class_study_prog as $prod) {
                        if (!in_array($prod->study_program_abbreviation, $a_class_study_prog)) {
                            array_push($a_class_study_prog, $prod->study_program_abbreviation);
                        }
                    }
                }

                $s_classprodi = ($mbo_class_study_prog) ? implode(' / ', $a_class_study_prog) : '-';
                $i_responden = ($mba_assessment_result) ? count($mba_assessment_result) : 0;
                $d_question = ($question_aspect_list) ? count($question_aspect_list) : 0;
                $c_col = $c_col_question;
                if ($question_aspect_list) {
                    $d_finaltotal_score = 0;
                    foreach ($question_aspect_list as $o_question) {
                        $mba_assessment_result = $this->Vm->get_lecturer_assessment_result([
                            'arq.question_id' => $o_question->question_id,
                            'ar.employee_id' => $o_lecturer->employee_id,
                            'sc.class_master_id' => $o_lecturer->class_master_id
                        ]);
                        $d_total_score_question = 0;
                        if ($mba_assessment_result) {
                            foreach ($mba_assessment_result as $key => $result) {
                                $d_total_score_question += $result->score_value;
                            }
                        }
                        
                        $d_finaltotal_score += $d_total_score_question;
                        $o_sheet->setCellValue($c_col++.$i_row, $d_total_score_question);
                    }
                    $d_average_score = $d_finaltotal_score / $i_responden / $d_question;
                    $d_average_score = number_format($d_average_score , 2, ".", ".");
                    $s_grade_assessment = $this->grades->lecturer_assessment_grade($d_average_score);
                    $o_sheet->setCellValue($c_col++.$i_row, $d_finaltotal_score);
                    $o_sheet->setCellValue($c_col++.$i_row, $d_average_score);
                    $o_sheet->setCellValue($c_col++.$i_row, $s_grade_assessment);
                }
                
                $o_sheet->setCellValue('A'.$i_row, $xnumber++);
                $o_sheet->setCellValue('B'.$i_row, $o_lecturer->personal_data_name);
                $o_sheet->setCellValue('C'.$i_row, $o_lecturer->subject_name);
                $o_sheet->setCellValue('D'.$i_row, $s_classprodi);
                $o_sheet->setCellValue('E'.$i_row, $i_responden);
                $i_row++;
            }

            $o_sheet->getStyle('A'.$i_startrow.':'.$c_colend.$i_row)->applyFromArray($style_border);

            $o_spreadsheet->createSheet();
            $o_sheetquestion = $o_spreadsheet->getSheet("1")->setTitle("Question");

            $o_sheetquestion->setCellValue('A1', "Qustion Number");
            $o_sheetquestion->setCellValue('A2', "Qustion Description");
            $i_rowquestion = 2;
            if ($question_aspect_list) {
                foreach ($question_aspect_list as $o_question) {
                    $o_sheetquestion->setCellValue("A".$i_rowquestion, $o_question->number);
                    $o_sheetquestion->setCellValue("B".$i_rowquestion, $o_question->question_desc);
                    $i_rowquestion++;
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_file_name);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
            readfile( $s_file_path.$s_file_name );
            exit;
        }

        print('<pre>');var_dump($mba_lectlist);exit;
    }

    function get_student_feeder_validate() {
        $mba_student_list = $this->Scm->get_student_by_score([
            'sc.academic_year_id' => 2022,
            'sc.score_approval' => 'approved',
        ], [7,8]);
        if ($mba_student_list) {
            print('<table border="1">');
            print('<tr><th>Student Name</th><th>NIM</th><th>Prodi</th><th>Batch</th><th>SKS Semester</th><th>IPS</th></tr>');
            foreach ($mba_student_list as $o_student) {
                $score_data = $this->Scm->get_score_data([
                    'sc.student_id' => $o_student->student_id,
                    'sc.academic_year_id' => 2022,
                    'sc.score_approval' => 'approved'
                ], [7,8]);
                $d_total_sks = 0;
                $d_total_merit = 0;
                $a_subject_list = [];

                if ($score_data) {
                    foreach ($score_data as $o_score_student) {
                        $d_gradepoin = $this->grades->get_grade_point($o_score_student->score_sum);
                        $d_merit = $this->grades->get_merit($o_score_student->curriculum_subject_credit, $d_gradepoin);

                        $d_total_sks += $o_score_student->curriculum_subject_credit;
                        $d_total_merit += $d_merit;
                    }
                }

                $d_ips = $this->grades->get_ipk($d_total_merit, $d_total_sks);

                print('<tr>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td>'.$o_student->student_number.'</td>');
                print('<td>'.$o_student->study_program_abbreviation.'</td>');
                print('<td>'.$o_student->student_batch.'</td>');
                print('<td>'.$d_total_sks.'</td>');
                print('<td>'.$d_ips.'</td>');
                print('</tr>');
            }
            print('</table>');
        }
        // print("<pre>");var_dump($mba_student_list);exit;
    }

    function get_lecturer_assessment_result() {
        $s_academic_year_id = 2022;
        $s_semester_type_id = 2;
        $mba_data = $this->Vm->get_lecturer_assessment_result([
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id
        ]);

        if ($mba_data) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Lecturer_Assessment_Result_'.$s_academic_year_id.$s_semester_type_id.'.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Academic Services")
                ->setCategory("Student Body Lecturer Assessment");
    
            $i_row = 1;

            $o_sheet->setCellValue('A'.$i_row, 'Student Name');
            $o_sheet->setCellValue('B'.$i_row, 'Batch');
            $o_sheet->setCellValue('C'.$i_row, 'Prodi');
            $o_sheet->setCellValue('D'.$i_row, 'Lecturer');
            $o_sheet->setCellValue('E'.$i_row, 'Subject');
            $o_sheet->setCellValue('F'.$i_row, 'Academic Semester');
            $o_sheet->setCellValue('G'.$i_row, 'Question');
            $o_sheet->setCellValue('H'.$i_row, 'Score');
            $o_sheet->setCellValue('I'.$i_row, 'Score Value');
            $o_sheet->setCellValue('J'.$i_row, 'Student Status  ');
            $i_row++;

            foreach ($mba_data as $o_result) {
                $o_sheet->setCellValue('A'.$i_row, $o_result->student_name);
                $o_sheet->setCellValue('B'.$i_row, $o_result->student_batch);
                $o_sheet->setCellValue('C'.$i_row, $o_result->prodi);
                $o_sheet->setCellValue('D'.$i_row, $o_result->lecturer);
                $o_sheet->setCellValue('E'.$i_row, $o_result->subject_name);
                $o_sheet->setCellValue('F'.$i_row, $o_result->academic_year_id.$o_result->semester_type_id);
                $o_sheet->setCellValue('G'.$i_row, $o_result->number.'. '.$o_result->question_desc);
                $o_sheet->setCellValue('H'.$i_row, $o_result->score_name);
                $o_sheet->setCellValue('I'.$i_row, $o_result->score_value);
                $o_sheet->setCellValue('J'.$i_row, $o_result->student_status);
                $i_row++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_file_name);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
            readfile( $s_file_path.$s_file_name );
            exit;
        }
        print('<pre>');var_dump($mba_data);exit;
    }

    public function cheat_billing($s_invoice_id, $b_force_print = false)
	{
        print('cheat inactive');exit;
		$i_now = time();
		
		// $mba_unpaid_invoice = $this->Im->get_unpaid_invoice();
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice(array('di.invoice_id' => $s_invoice_id));
        // $o_invoice = json_decode($s_json_string);
        // print('<pre>');
        // var_dump($mba_unpaid_invoice);exit;
		if($mba_unpaid_invoice){
            // print('ada');exit;
			foreach($mba_unpaid_invoice as $o_invoice){
				$b_send_reminder = false;
				$b_allow_fine = false;
				
				$mba_sub_invoice_details = $this->Im->get_invoice_data(['di.invoice_id' => $o_invoice->invoice_id]);
                $mba_invoice_detail = $this->Im->student_has_invoice_list($o_invoice->personal_data_id, [
                    'df.fee_amount_type' => 'main'
                ]);
                // print('<pre>');var_dump($mba_sub_invoice_details);exit;
				if($mba_sub_invoice_details){
					$a_sub_invoice_details_fined = array();
					$o_sub_invoice_details_fined = false;
					
					foreach($mba_sub_invoice_details as $o_sub_invoice_details){
                        // print('<pre>');var_dump($mba_invoice_detail);exit;
						$i_deadline = strtotime($o_sub_invoice_details->sub_invoice_details_deadline);
						$i_datediff = $i_now - $i_deadline;
						$i_float = round($i_datediff / (60 * 60 * 24));
						
						if($i_float == 0){
                            // print('float: 0 -> '.$o_sub_invoice_details->sub_invoice_details_id);
							if($o_invoice->invoice_allow_fine == 'yes'){
								if($o_sub_invoice_details->sub_invoice_type == 'installment'){
									array_push($a_sub_invoice_details_fined, $o_sub_invoice_details);
								}
								else{
									$o_sub_invoice_details_fined = $o_sub_invoice_details;
								}
								$b_allow_fine = true;
							}
						}
						else {
                            // if($i_float >= -14 AND $i_float < 0) {
                                // if (is_null($o_sub_invoice_details->trx_id)) {
                                //     // print('null trx_id: '.$o_invoice->personal_data_name.' - '.$o_sub_invoice_details->sub_invoice_details_va_number);
                                // }else{
                                //     // print($o_invoice->personal_data_name.'send reminder - '.$o_sub_invoice_details->sub_invoice_details_va_number);
                                // }
                                if(is_null($o_sub_invoice_details->trx_id)){
                                    // create va
                                    // print('yes');exit;
                                    if ($o_sub_invoice_details->sub_invoice_details_status != 'paid') {
                                        $a_trx_data = array(
                                            'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount,
                                            'billing_type' => 'c',
                                            'customer_name' => $o_invoice->personal_data_name,	
                                            'virtual_account' => $o_sub_invoice_details->sub_invoice_details_va_number,
                                            'description' => $o_sub_invoice_details->sub_invoice_details_description,
                                            'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +10 day")),
                                            'customer_email' => 'bni.employee@company.ac.id'
                                        );

                                        if (($mba_invoice_detail) AND ($mba_invoice_detail[0]->payment_type_code == '14')) {
                                            $a_trx_data['trx_amount'] = 0;
                                            $a_trx_data['billing_type'] = 'o';
                                        }

                                        $a_bni_result = $this->Bnim->create_billing($a_trx_data);
                                        
                                        $a_sub_invoice_details_update = array(
                                            'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline))
                                        );
                                        
                                        if($a_bni_result['status'] == '000'){
                                            $a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
                                        }
                                        else{
                                            // print('<pre>');var_dump($a_bni_result);exit;
                                            if($a_bni_result['status'] == '102'){
                                                $a_update_billing = array(
                                                    'trx_id' => $a_bni_result['trx_id'],
                                                    'trx_amount' => 999,
                                                    'customer_name' => 'CANCEL PAYMENT',
                                                    'datetime_expired' => '2020-01-01 23:59:59',
                                                    'description' => 'CANCEL PAYMENT'
                                                );
                                                $this->Bnim->update_billing($a_update_billing);
                                                print('Cancel Billing !'.$o_sub_invoice_details->sub_invoice_details_va_number);
                                            }
                                            else{
                                                $this->email->from('employee@company.ac.id');
                                                $this->email->to(array());
                                                $this->email->subject('ERROR CHECK BILLING');
                                                $this->email->message(json_encode($a_bni_result));
                                                $this->email->send();
                                                // print('failed!');
                                            }
                                        }
                                        
                                        $this->Im->update_sub_invoice_details(
                                            $a_sub_invoice_details_update, 
                                            array(
                                                'sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id
                                            )
                                        );

                                        if ($b_force_print) {
                                            print('<pre>');var_dump($a_bni_result);
                                        }
                                    }
                                }
                                // send reminder
                                $mba_invoice_details = $this->Im->get_invoice_details([
                                    'did.invoice_id' => $o_invoice->invoice_id,
                                    'df.payment_type_code' => '02',
                                    'df.semester_id != ' => 1
                                ]);
                                
                                if($mba_invoice_details){
                                    if($o_sub_invoice_details->sub_invoice_details_status != 'paid'){
                                        $b_send_reminder = true;
                                    }
                                }
                            // }else{
                            //     print($i_float);
                            // }
                        }
					}
					
					// if($b_allow_fine){
					// 	// $this->set_fine($o_invoice, $a_sub_invoice_details_fined, $o_sub_invoice_details_fined);
					// }
					
					// if($b_send_reminder){
                    //     // $this->send_reminder($o_invoice);
                    //     print('reminder send!');
                    //     modules::run('callback/api/send_reminder', $o_invoice);
					// }
				}
			}
		}
        // else if ($b_force_print) {
        //     print('<pre>');var_dump('invoice is not unpaid');
        // }
        else {
            print('<pre>');var_dump('invoice is not unpaid');
        }
    }
    
    public function check_trx_id()
    {
        $s_trx_id = '166279029';
        $a_bni_va = $this->Bnim->check_inquiry_billing($s_trx_id, true);
        print('<pre>');
        var_dump($a_bni_va);exit;
        // print(json_encode($a_bni_va));
    }
    
    public function beberes_invoice()
    {
        $mba_unpaid_invoice = $this->General->get_where('dt_invoice', ['invoice_status !=' => 'paid', 'invoice_allow_reminder' => 'yes']);
        
        $a_invoice_cancelled = [];
        $a_personal_data_name = [];
        $a_invoice_id_paid = [];
        $a_invoice_must_canceled = [];
        $a_va_is_null = [];
        $a_va_has_paid = [];
        $a_trx_id_error = [];
        $a_trx_inactive = [];
        foreach ($mba_unpaid_invoice as $o_invoice) {
            $mbo_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $o_invoice->personal_data_id])[0];
            $mbo_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_invoice->personal_data_id])[0];
            // check installment
            if ($o_invoice->invoice_status != 'cancelled') {
                $mba_sub_invoice_installment = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $o_invoice->invoice_id, 'sub_invoice_type' => 'installment'])[0];

                if (($mbo_student_data->student_status == 'pending') AND ($mbo_student_data->academic_year_id <= 2020)) {
                    $s_invoice_data_must_canceled = $o_invoice->invoice_id." ".$mbo_personal_data->personal_data_name."-".$mbo_student_data->finance_year_id;
                    if (!in_array($s_invoice_data_must_canceled, $a_invoice_must_canceled)) {
                        array_push($a_invoice_must_canceled, $s_invoice_data_must_canceled);
                    }
                }else{
                    if ($mba_sub_invoice_installment) {
                        $mba_unpaid_va = $this->General->get_where('dt_sub_invoice_details', [
                            'sub_invoice_id' => $mba_sub_invoice_installment->sub_invoice_id,
                            'sub_invoice_details_amount_paid' => 0,
                            'sub_invoice_details_status !=' => 'paid'
                        ]);
    
                        if ($mba_unpaid_va) {
                            foreach ($mba_unpaid_va as $o_sub_details) {
                                if (is_null($o_sub_details->trx_id)) {
                                    if (!in_array($o_sub_details->sub_invoice_details_va_number, $a_va_is_null)) {
                                        array_push($a_va_is_null, $o_sub_details->sub_invoice_details_va_number);
                                    }
                                }else{
                                    $a_bni_va = $this->Bnim->inquiry_billing($o_sub_details->trx_id, true);
                                    if (array_key_exists('status', $a_bni_va)) {
                                        if (!in_array($o_sub_details->trx_id, $a_trx_id_error)) {
                                            array_push($a_trx_id_error, $o_sub_details->trx_id);
                                        }
                                    }else{
                                        $s_trx_student_data = $o_sub_details->trx_id." ".$mbo_personal_data->personal_data_name."-".$mbo_student_data->student_status;
    
                                        if ($a_bni_va['payment_amount'] != '0') {
                                            if (!in_array($s_trx_student_data, $a_va_has_paid)) {
                                                array_push($a_va_has_paid, $s_trx_student_data);
                                            }
                                        }else if ($a_bni_va['va_status'] == '2') {
                                            if ($mbo_student_data->student_status == 'active') {
                                                $activated = modules::run('finance/invoice/activate_virtual_account', $o_sub_details->sub_invoice_details_id, false);
                                                if ($activated['code'] == 0) {
                                                    $s_trx_student_data .= "(activated)";
                                                }else{
                                                    $s_trx_student_data .= "(".$activated['message'].")";
                                                }
                                            }else if ($mbo_student_data->student_status == 'resign') {
                                                if (!in_array($o_invoice->invoice_id, $a_invoice_cancelled)) {
                                                    // $this->General->update_data('dt_invoice', ['invoice_status' => 'cancelled'], ['invoice_id' => $o_invoice->invoice_id]);
                                                    array_push($a_invoice_cancelled, $o_invoice->invoice_id);
                                                }
                                            }

                                            if (!in_array($s_trx_student_data, $a_trx_inactive)) {
                                                array_push($a_trx_inactive, $s_trx_student_data);
                                            }
                                        }
                                    }
                                }
                            }
                        }
    
                        $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', ['sub_invoice_id' => $mba_sub_invoice_installment->sub_invoice_id]);
                        $a_count_invoice_details_paid = 0;
    
                        if ($mba_sub_invoice_details) {
                            foreach ($mba_sub_invoice_details as $o_sub_invoice_details) {
                                if ($o_sub_invoice_details->sub_invoice_details_amount_paid > 0) {
                                    $a_count_invoice_details_paid++;
                                }
                            }
    
                            if ($a_count_invoice_details_paid == count($mba_sub_invoice_details)) {
                                if (!in_array($o_invoice->invoice_id, $a_invoice_id_paid)) {
                                    array_push($a_invoice_id_paid, $o_invoice->invoice_id);
                                }
                            }
                        }
                    }
                }

            }
        }

        print('<pre>');
        print('<br>');
        print('<h2>List Invoice Paid</h2>');
        print_r($a_invoice_id_paid);
        print('<br>');
        print('<h2>List Invoice Student Pending</h2>');
        print_r($a_invoice_must_canceled);
        print('<br>');
        print('<h2>List trx_id is null</h2>');
        print_r($a_va_is_null);
        print('<br>');
        print('<h2>List Virtual Account Has Paid</h2>');
        print_r($a_va_has_paid);
        print('<br>');
        print('<h2>List trx_id error</h2>');
        print_r($a_trx_id_error);
        print('<br>');
        print('<h2>List trx_id inactive</h2>');
        print_r($a_trx_inactive);
    }

    function get_student_va()
    {
        $s_va_number = $this->Bnim->get_va_number(
            '02',//     $s_payment_type_code,
            '6',//     $i_semester,
            '0',//     $i_installments,
            'student', //     $s_student_type,
            '11201701021', //     $s_student_number = null,
            '2017', //     $i_year = null,
            '1' // program_id
        );

        print('<pre>');
        var_dump($s_va_number);exit;
    }

    public function generate_student_score($s_class_master_id)
    {
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        if ($mbo_class_master_data) {
            $this->load->model('study_program/Study_program_model', 'Spm');
            $s_file_name = str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));
            $s_folder_master = $s_file_name.'_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$s_class_master_id;

            $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

            if(!file_exists($path_master)){
                mkdir($path_master, 0777, TRUE);
            }

            $s_file = 'Student_score_'.$s_file_name.'_'.$mbo_class_master_data->running_year.'_'.$mbo_class_master_data->class_semester_type_id.'_'.$mbo_class_master_data->class_master_id;

            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');

            fputcsv($fp, [
                'Student Number', 'Student Name', 'Study Program', 'Subject', 'Absence', 'Score  Quiz', 'Final Exam', 'Repetition Exam', 'Final Score', 'Grade'
            ]);
            
            // $mba_score_list = $this->Scm->get_score_data(['sc.class_master_id' => $s_class_master_id]);
            $mba_score_list = $this->Cgm->get_class_master_student($s_class_master_id);

            if ($mba_score_list) {
                foreach ($mba_score_list as $o_score) {
                    $mbo_study_program_data = $this->Stm->get_student_by_id($o_score->student_id);
                    
                    fputcsv($fp, [
                        $o_score->student_number,
                        $mbo_study_program_data->personal_data_name,
                        $mbo_study_program_data->study_program_name,
                        str_replace("&amp;", " and ", $o_score->subject_name),
                        '="'.$o_score->score_absence.'"',
                        $o_score->score_quiz,
                        $o_score->score_final_exam,
                        $o_score->score_repetition_exam,
                        $o_score->score_sum,
                        $o_score->score_grade
                    ]);
                }
            }
            
            print('<pre>');
            var_dump($mba_score_list);exit;
        }
    }

    public function generate_lecturer_absence($s_class_master_id)
    {
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        if ($mbo_class_master_data) {
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($s_class_master_id, false, 'ASC');

            $s_file_name = str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));
            $s_folder_master = $s_file_name.'_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$s_class_master_id;

            $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

            if(!file_exists($path_master)){
                mkdir($path_master, 0777, TRUE);
            }

            $s_file = 'Lecturer_absence_'.$s_file_name.'_'.$mbo_class_master_data->running_year.'_'.$mbo_class_master_data->class_semester_type_id.'_'.$mbo_class_master_data->class_master_id;

            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');

            fputcsv($fp, [
                'Lecturer', 'Subject', 'Semester', 'Start Time', 'End Time', 'Topics Covered'
            ]);

            if ($mba_uosd_list) {
                foreach ($mba_uosd_list as $o_uosd) {
                    fputcsv($fp, [
                        $o_uosd->personal_data_name,
                        str_replace("&", " and ", $mbo_class_master_data->subject_name),
                        $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id,
                        $o_uosd->subject_delivered_time_start,
                        $o_uosd->subject_delivered_time_end,
                        str_replace("&amp;", " and ", $o_uosd->subject_delivered_description)
                    ]);
                }
            }

            fclose($fp);
            // print('<pre>');
            // var_dump($mba_uosd_list);exit;
        }
    }

    public function genereate_student_absence($s_class_master_id)
    {
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        if ($mbo_class_master_data) {
            // print('<pre>');
            // var_dump($mbo_class_master_data);exit;
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($s_class_master_id, false, 'ASC');
            $mba_student_list = $this->Cgm->get_class_master_student($s_class_master_id);

            $s_file_name = str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));
            $s_folder_master = $s_file_name.'_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$s_class_master_id;

            $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

            if(!file_exists($path_master)){
                mkdir($path_master, 0777, TRUE);
            }

            $s_file = 'Student_absence_'.$s_file_name.'_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$mbo_class_master_data->class_master_id;

            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');
            
            $a_header = [" "];
            if ($mba_uosd_list) {
                foreach ($mba_uosd_list as $o_uosd) {
                    array_push($a_header, $o_uosd->subject_delivered_time_start);
                }
            }

            fputcsv($fp, $a_header);

            $a_data = [];
            if ($mba_student_list) {
                foreach ($mba_student_list as $o_student) {
                    $a_student_absence = [];
                    array_push($a_student_absence, $o_student->personal_data_name);

                    if ($mba_uosd_list) {
                        foreach ($mba_uosd_list as $o_uosd) {
                            $mba_student_absence = $this->Scm->get_student_absence(['sc.score_id' => $o_student->score_id, 'as.subject_delivered_id' => $o_uosd->subject_delivered_id])[0];
                            array_push($a_student_absence, (($mba_student_absence) ? $mba_student_absence->absence_status : ''));
                        }
                    }

                    array_push($a_data, $a_student_absence);
                }
            }

            if (count($a_data) > 0) {
                foreach ($a_data as $o_data) {
                    fputcsv($fp, $o_data);
                }
            }

            fclose($fp);
        }
    }

    public function generate_class_report($s_class_master_id)
    {
        $this->generate_lecturer_absence($s_class_master_id);
        $this->genereate_student_absence($s_class_master_id);
        $this->generate_student_score($s_class_master_id);
    }

    public function check_array()
    {
        $a_array = [
            [
                'student_id' => 'dd4e66c9-7ef8-43ae-b130-f674e846fd5d',
                'semester_id' => 9,
                'payment_type_id' => '02',
                'row' => 3
            ],
            [
                'student_id' => '9512749e-2554-412d-a791-822e07148f6c',
                'semester_id' => 0,
                'payment_type_id' => '04',
                'row' => 4
            ],
            [
                'student_id' => '5d546dc2-da4d-427e-acde-29cee88450eb',
                'semester_id' => 5,
                'payment_type_id' => '02',
                'row' => 5
            ],
            [
                'student_id' => '9512749e-2554-412d-a791-822e07148f6c',
                'semester_id' => 1,
                'payment_type_id' => '02',
                'row' => 6
            ]
        ];

        $student_id = '9512749e-2554-412d-a791-822e07148f6ca';
        $payment_type_id = '02';
        $semester_id = 1;

        $filtered_array = array_filter($a_array, function($val) use($student_id, $payment_type_id, $semester_id){
            return ($val['student_id'] == $student_id AND $val['payment_type_id'] == $payment_type_id AND $val['semester_id'] == $semester_id);
        });

        // $index = array_search('9512749e-2554-412d-a791-822e07148f6c', array_column($a_array, 'student_id'));
        // print($index);

        // print('<pre>');
        // print_r($a_array[$index]);
        print('<pre>');
        var_dump($filtered_array);
    }

    public function convert_report_bni()
    {
        $s_file = APPPATH."uploads/finance/billdata-20200620_20200921_5446_215101.csv";
        $data = file($s_file);

        $a_candidate_status = ['candidate', 'pending', 'participant'];
        $a_input_data = array();

        if (count($data) > 0) {
            $a_va_number = [];
            $a_student_id = [];
            unset($data[0]);

            $z = 0;

            foreach ($data as $line => $s_line_data) {
                $z++;

                if ($z == 10) {
                    break;
                }
                $s_line_data = str_replace('"', '', $s_line_data);
                $s_line_data = str_replace('=', '', $s_line_data);
                $a_data = explode(';', $s_line_data);

                $s_payment_type_id = substr($a_data[2], 4, 2);
                $i_semester_id = intval(substr($a_data[2], 6, 2));

                $mba_invoice_data = $this->Im->get_invoice_data(['dsid.sub_invoice_details_va_number' => $a_data[2]])[0];

                if ($mba_invoice_data) {
                    $mba_student_data = $this->General->get_where('dt_student', [
                        'personal_data_id' => $mba_invoice_data->personal_data_id
                    ]);

                    if ($mba_student_data) {
                        $mbo_student_data = false;
                        if (count($mba_student_data) > 1) {
                            
                            foreach ($mba_student_data as $o_student) {
                                if ($o_student->student_status == 'active') {
                                    $mbo_student_data = $o_student;
                                }
                            }

                        }else{
                            $mbo_student_data = $mba_student_data[0];
                        }

                        if (!$mbo_student_data) {
                            $i = 0;
                            $s_timestamp = $mba_student_data[$i]->timestamp;
                            foreach ($mba_student_data as $key => $o_student) {
                                if (date('Y-m-d H:i:s', strtotime($o_student->timestamp)) > date('Y-m-d H:i:s', strtotime($s_timestamp))) {
                                    $i = $key;
                                }
                            }

                            $mbo_student_data = $mba_student_data[$i];
                        }
                        
                        $s_student_id = $mbo_student_data->student_id;

                        if (count($a_input_data) == 0) {
                            array_push($a_input_data, [
                                'student_id' => $mbo_student_data->student_id,
                                'payment_type_id' => $s_payment_type_id,
                                'semester_id' => $i_semester_id,
                                'row' => 'excel row'
                            ]);
                        }else{
                            $a_data_exist = array_filter($a_input_data, function($val) use($s_student_id, $s_payment_type_id, $i_semester_id) {
                                return ($val['student_id'] == $s_student_id AND $val['payment_type_id'] == $s_payment_type_id AND $val['semester_id'] == $i_semester_id);
                            });
    
                            if (count($a_data_exist) == 0) {
                                array_push($a_input_data, [
                                    'student_id' => $mbo_student_data->student_id,
                                    'payment_type_id' => $s_payment_type_id,
                                    'semester_id' => $i_semester_id,
                                    'row' => 'excel row'
                                ]);
                            }else{
                                // cetak di excel
                            }
                        }

                    }else{
                        print('student ngga ada: '.$mba_invoice_data->personal_data_id);exit;
                    }
                }else{
                    $mba_portal_invoice_data = false;

                    if ($mba_portal_invoice_data) {
                        # code...
                    }else{
                        print('invoice ngga ada: '.$a_data[3].' va-'.$a_data[2]);exit;
                    }

                }
                
            }

            print('<pre>');
            var_dump($a_input_data);

            // print('<pre>');
            // var_dump($a_input_data);

            print('finish check');

            // student_id, payment_type_id, semester
        }
    }

    public function read_report_bni()
    {
        $s_file = APPPATH."uploads/finance/billdata-20200620_20200921_5446_215101.csv";
        $data = file($s_file);
        if (count($data) > 0) {
            print('<pre>');
            $a_va_number = [];
            $a_student_id = [];
            unset($data[0]);

            foreach ($data as $line => $s_line_data) {
                // $mbo_student_by_va_number =
                
                $s_line_data = str_replace('"', '', $s_line_data);
                $s_line_data = str_replace('=', '', $s_line_data);
                $a_data = explode(';', $s_line_data);

                if (!in_array($a_data[2], $a_va_number)) {
                    // array_push($a_va_number, [$line => $a_data[2]]);
                    $a_va_number[(string)$line] = $a_data[2];
                }else{
                    $key_a_va_number = array_search($a_data[2], $a_va_number);

                    $temp_data = $data[$key_a_va_number];
                    $temp_data = str_replace('"', '', $s_line_data);
                    $temp_data = str_replace('=', '', $s_line_data);
                    $a_temp_data = explode(';', $temp_data);

                    if (intval($a_temp_data[8]) < $a_data[8]) {
                        $a_va_number[(string)$key_a_va_number] = $a_data[2];
                    }else if (($a_temp_data[11] == '2') AND ($a_data[11] == '1')) {
                        $a_va_number[(string)$key_a_va_number] = $a_data[2];
                    }else if (date('Y-m-d H:i:s', strtotime($a_temp_data[9])) < date('Y-m-d H:i:s', strtotime($a_data[9]))) {
                        $a_va_number[(string)$key_a_va_number] = $a_data[2];
                    }

                }
                    
            }
            var_dump($a_va_number);
            
        }
        
    }

    public function test_parsing_semester($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data->student_type != 'transfer') {
            $mbo_student_start_semester = $this->Smm->get_student_start_semester($s_student_id);
        }else{
            $mbo_student_start_semester = $this->Scm->get_last_score([
                'sc.student_id' => $s_student_id
            ], 'ASC');
        }

        $mba_semester_list = $this->Smm->get_semester_setting_list();
        $semester = 0;
        
        if ($mba_semester_list) {
            $b_start = false;
            foreach ($mba_semester_list as $o_semester) {
                if (($o_semester->academic_year_id == $mbo_student_start_semester->academic_year_id) AND ($o_semester->semester_type_id == $mbo_student_start_semester->semester_type_id)) {
                    $b_start = true;
                }

                if ($b_start) {
                    $semester++;
                }

                if (($o_semester->academic_year_id == $s_academic_year_id) AND ($o_semester->semester_type_id == $s_semester_type_id)) {
                    break;
                }
                
            }

            // print('<pre>');
            // var_dump($semester);
        }

        return $semester;

    }

    public function get_invoice_report_semester()
    {
        $mba_student_active = $this->Stm->get_student_filtered([
            'ds.student_status' => 'active'
        ]);

        if ($mba_student_active) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Student_body.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Academic Services")
                ->setCategory("Student Body Invoice");
    
            $i_row = 1;

            $o_sheet->setCellValue('A'.$i_row, 'Student Name');
            $o_sheet->setCellValue('B'.$i_row, 'Student Number');
            $o_sheet->setCellValue('C'.$i_row, 'Batch');
            $o_sheet->setCellValue('D'.$i_row, 'Fac');
            $o_sheet->setCellValue('E'.$i_row, 'Prodi');
            $o_sheet->setCellValue('F'.$i_row, 'Status');

            $o_sheet->mergeCells("A$i_row:A".($i_row + 1));
            $o_sheet->mergeCells("B$i_row:B".($i_row + 1));
            $o_sheet->mergeCells("C$i_row:C".($i_row + 1));
            $o_sheet->mergeCells("D$i_row:D".($i_row + 1));
            $o_sheet->mergeCells("E$i_row:E".($i_row + 1));
            $o_sheet->mergeCells("F$i_row:F".($i_row + 1));
            
            $o_sheet->setCellValue('G'.$i_row, 'Unpaid Tuition Fee (Current Semester - 2022/2) (IDR)');
            $o_sheet->setCellValue('Q'.$i_row, 'Unpaid Tuition Fee (Previous Semester) (IDR)');

            $o_sheet->mergeCells("G$i_row:P$i_row");
            $o_sheet->mergeCells("Q$i_row:Q".($i_row + 1));

            $i_row++;
            $o_sheet->setCellValue('G'.$i_row, '1');
            $o_sheet->setCellValue('H'.$i_row, '2');
            $o_sheet->setCellValue('I'.$i_row, '3');
            $o_sheet->setCellValue('J'.$i_row, '4');
            $o_sheet->setCellValue('K'.$i_row, '5');
            $o_sheet->setCellValue('L'.$i_row, '6');
            $o_sheet->setCellValue('M'.$i_row, '7');
            $o_sheet->setCellValue('N'.$i_row, '8');
            $o_sheet->setCellValue('O'.$i_row, '9');
            $o_sheet->setCellValue('P'.$i_row, '10');

            $i_row++;
            $a_installment_col = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];

            foreach ($mba_student_active as $o_student) {
                $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('B'.$i_row, $o_student->student_number);
                $o_sheet->setCellValue('C'.$i_row, $o_student->academic_year_id);
                $o_sheet->setCellValue('D'.$i_row, $o_student->faculty_abbreviation);
                $o_sheet->setCellValue('E'.$i_row, $o_student->study_program_abbreviation);
                $o_sheet->setCellValue('F'.$i_row, $o_student->student_status);

                $mba_student_invoice = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                    'df.payment_type_code' => '02',
                    'di.academic_year_id' => 2022,
                    'di.semester_type_id' => 2,
                    'di.invoice_status != ' => 'cancelled'
                ]);

                $a_invoice_filter = [
                    'df.payment_type_code' => '02',
                ];

                if ($mba_student_invoice) {
                    $a_invoice_filter['di.invoice_id != '] = $mba_student_invoice[0]->invoice_id;
                }
                $d_amount_pending = 0;

                $mba_student_invoice_list = $this->Im->student_has_invoice_list($o_student->personal_data_id, $a_invoice_filter, ['created', 'pending']);
                if ($mba_student_invoice) {
                    $o_student_invoice = $mba_student_invoice[0];
                    $mba_installment = $this->Im->get_invoice_installment($o_student_invoice->invoice_id);
                    if ($mba_installment) {
                        // $i_installment = 0;
                        foreach ($mba_installment as $i_installment => $o_installment) {
                            $d_print = $o_installment->sub_invoice_details_amount_total;
                            if ($o_installment->sub_invoice_details_status == 'paid') {
                                $d_print = 0;
                            }
                            $o_sheet->setCellValue($a_installment_col[$i_installment].$i_row, $d_print);
                        }
                    }
                }

                // if ($o_student->student_number == '11201602002') {
                //     print('<pre>');var_dump($a_invoice_filter);exit;
                // }

                if ($mba_student_invoice_list) {
                    foreach ($mba_student_invoice_list as $o_invoice) {
                        $o_invoice_full = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                        $a_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                        $d_amount_unpaid = 0;

                        if ($a_invoice_installment) {
                            foreach ($a_invoice_installment as $installment) {
                                if ($installment->sub_invoice_details_status != 'paid') {
                                    $d_amount_unpaid += $installment->sub_invoice_details_amount;
                                }
                            }
                        }
                        else if ($o_invoice_full) {
                            if ($o_invoice_full->sub_invoice_details_status != 'paid') {
                                $d_amount_unpaid += $o_invoice_full->sub_invoice_details_amount;
                            }
                        }
                        $d_amount_pending += $d_amount_unpaid;
                    }
                }

                $d_amount_total_pending = number_format($d_amount_pending, 0, '.', '.');
                $o_sheet->setCellValue('Q'.$i_row, $d_amount_total_pending);
                $i_row++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_file_name);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
            readfile( $s_file_path.$s_file_name );
            exit;
        }
    }

    public function copy_ofse_subject()
    {
        $this->load->model('academic/Offered_Subject_model', 'Osm');
        $this->load->model('academic/Curriculum_model', 'Crm');

        $s_academic_year_id = '2022';
        $s_semester_type_id = '2';
        $s_ofse_period_id = '2c77574c-8112-4ae0-a1de-b8a96e55cefa';
        
        $mba_ofse_offered_subject_data = $this->General->get_where('dt_offered_subject', ['ofse_period_id' => 'f134822c-c839-4302-9fcb-05d18143e43b']);
        if ($mba_ofse_offered_subject_data) {
            foreach ($mba_ofse_offered_subject_data as $o_offered_subejct) {
                $a_return = [];
                $mba_check_offer_subject_exists = $this->General->get_where('dt_offered_subject', [
                    'curriculum_subject_id' => $o_offered_subejct->curriculum_subject_id,
                    'ofse_period_id' => $s_ofse_period_id,
                    'study_program_id' => $o_offered_subejct->study_program_id,
                    'ofse_status' => $o_offered_subejct->ofse_status
                ]);
                if (!$mba_check_offer_subject_exists) {
                    $s_offered_subject_id = $this->uuid->v4();
                    $mbo_subject_data = $this->Crm->get_curriculum_subject_data($o_offered_subejct->curriculum_subject_id);
                    if (!$mbo_subject_data) {
                        print('<pre>');var_dump('subject not found '.$o_offered_subejct->curriculum_subject_id);exit;
                    }

                    $a_data = [
                        'offered_subject_id' => $s_offered_subject_id,
                        'curriculum_subject_id' => $o_offered_subejct->curriculum_subject_id,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'program_id' => 1,
                        'study_program_id' => $o_offered_subejct->study_program_id,
                        'ofse_period_id' => $s_ofse_period_id,
                        'ofse_status' => $o_offered_subejct->ofse_status,
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    if ($this->Osm->save_offer_subject($a_data)) {
                        $s_class_group_id = $this->uuid->v4();

                        $a_class_group_data = [
                            'class_group_id' => $s_class_group_id,
                            'academic_year_id' => $s_academic_year_id,
                            'semester_type_id' => $s_semester_type_id,
                            'class_group_name' => 'OFSE '.$mbo_subject_data->subject_name,
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        $a_class_group_subject_data = [
                            'class_group_subject_id' => $this->uuid->v4(),
                            'class_group_id' => $s_class_group_id,
                            'offered_subject_id' => $s_offered_subject_id,
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        if ($this->Cgm->save_data($a_class_group_data)) {
                            if ($this->Cgm->save_class_group_subject($a_class_group_subject_data)) {
                                $a_return = ['code' => 0, 'message' => 'Success'];
                            }else{
                                $a_return = ['code' => 1, 'message' => 'Fail processing subject!'];
                            }
                        }else{
                            $a_return = ['code' => 1, 'message' => 'Fail processing class!'];
                        }
                    }else{
                        $a_return = ['code' => 1, 'message' => 'Fail submitting data!'];
                    }
                }

                print('<pre>');var_dump($a_return);
                print('<br>');
            }
        }
    }

    public function force_ofse_score()
    {
        print('closed!');exit;
        $s_template_path = APPPATH.'uploads/temp/ofse score xx.xlsx';
        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $i_row = 2;
        $i_insert = 0;
        while ($o_sheet->getCell('A'.$i_row)->getValue() !== NULL) {
            $s_student_id = trim($o_sheet->getCell('A'.$i_row)->getValue());
            $s_curriculum_subject_id = trim($o_sheet->getCell('D'.$i_row)->getValue());
            $s_score_1 = trim($o_sheet->getCell('E'.$i_row)->getValue());
            $s_score_2 = trim($o_sheet->getCell('F'.$i_row)->getValue());
            $s_score_final = (doubleval($s_score_1) + doubleval($s_score_2)) / 2;
            $ofse_ready = $this->General->get_where('dt_score', [
                'student_id' => $s_student_id,
                'curriculum_subject_id' => $s_curriculum_subject_id,
                'semester_id' => 17
            ]);

            $s_examiner = [
                'score_examiner_1' => $s_score_1,
                'score_examiner_2' => $s_score_2
            ];
            
            if (!$ofse_ready) {
                $a_score_data = [
                    'score_id' => $this->uuid->v4(),
                    'student_id' => $s_student_id,
                    'curriculum_subject_id' => $s_curriculum_subject_id,
                    'semester_id' => '17',
                    'semester_type_id' => '4',
                    'academic_year_id' => '2019',
                    'score_quiz' => $s_score_final,
                    'score_final_exam' => $s_score_final,
                    'score_sum' => $s_score_final,
                    'score_grade' => $this->grades->get_grade($s_score_final),
                    'score_approval' => 'approved',
                    'score_examiner' => json_encode($s_examiner)
                ];

                $i_insert++;
                // $this->db->update('dt_score', $a_score_data, [
                //     'score_id' => $ofse_ready[0]->score_id
                // ]);
                $this->db->insert('dt_score', $a_score_data);
                print('<pre>');var_dump($this->db->last_query());
                print('<br>');
            }
            $i_row++;
        }

        print ('<h1>'.$i_insert.'</h1>');
    }

    public function push_ofse_score($s_score_id, $s_score_1, $s_score_2)
    {
        $a_score_examiner = [
            'score_examiner_1' => $s_score_1,
            'score_examiner_2' => $s_score_2
        ];

        $s_score_sum = ($s_score_1 + $s_score_2) / 2;
        // $s_score_sum = round($s_score_sum, 0, PHP_ROUND_HALF_UP);
        $s_score_grade = $this->grades->get_grade($s_score_sum);
        print($s_score_sum);
        print('<br>');
        print($s_score_grade);

        $this->db->update('dt_score', [
            'score_quiz' => $s_score_sum,
            'score_final_exam' => $s_score_sum,
            'score_sum' => $s_score_sum,
            'score_grade' => $s_score_grade,
            'score_examiner' => json_encode($a_score_examiner)
        ], [
            'score_id' => $s_score_id
        ]);

        print('<br>');
        if ($this->db->affected_rows() > 0) {
            print('updated : '.$this->db->affected_rows());
        }else{
            print('not updated!');
        }
    }

    public function status_mahasiswa_per_semester($s_prodi_main_id = '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8')
    {
        $this->load->library('FeederAPI');
        $a_status_allowed = ['active', 'inactive', 'onleave', 'resign', 'graduated', 'dropout'];
        $mba_semester_list = $this->Smm->get_semester_setting(['dss.academic_year_id <= ' => '2020']);
        $prodi_data_main = $this->General->get_where('ref_study_program', ['study_program_main_id' => $s_prodi_main_id]);
        $prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_prodi_main_id]);
        if ($prodi_data_main) {
            $prodi_data = array_merge($prodi_data, $prodi_data_main);
        }
        
        if (!$prodi_data) {
            print('prodi not found!');exit;
        }
        if (!$mba_semester_list) {
            print('semester not found!');exit;
        }

        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_file_name = 'Student_body_'.str_replace(' ', '_', $prodi_data[0]->study_program_name_feeder).'.xlsx';
        $s_file_path = APPPATH."uploads/temp/";

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }
        
        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $o_spreadsheet->getProperties()
            ->setTitle($s_file_name)
            ->setCreator("IULI Academic Services")
            ->setCategory("Student Body ".$prodi_data[0]->study_program_name_feeder);

        $i_row = 1;
        $c_semester = 'F';
        $o_sheet->setCellValue('A'.$i_row, 'Student Name');
        $o_sheet->setCellValue('B'.$i_row, 'Student ID Number');
        $o_sheet->setCellValue('C'.$i_row, 'Student Type');
        $o_sheet->setCellValue('D'.$i_row, 'Prodi');
        $o_sheet->setCellValue('E'.$i_row, 'Batch');
        $o_sheet->setCellValue('F'.$i_row, 'Status per akademik semester');
        $o_sheet->mergeCells('A1:A2');
        $o_sheet->mergeCells('B1:B2');
        $o_sheet->mergeCells('C1:C2');
        $o_sheet->mergeCells('D1:D2');
        $o_sheet->mergeCells('E1:E2');
        $i_row++;

        $c_semester_header = $c_semester;
        foreach ($mba_semester_list as $o_semester) {
            $o_sheet->setCellValue($c_semester_header.$i_row, $o_semester->academic_year_id.$o_semester->semester_type_id);
            $c_semester_header++;
        }
        $c_semester_header = chr(ord($c_semester_header) - 1);
        $o_sheet->mergeCells($c_semester.'1:'.$c_semester_header.'1');

        $i_row++;
        // print('<pre>');var_dump($prodi_data);exit;

        foreach ($prodi_data as $o_prodi) {
            $mba_student_data = $this->Stm->get_student_filtered([
                'ds.study_program_id' => $o_prodi->study_program_id
            ], $a_status_allowed);

            if ($mba_student_data) {
                foreach ($mba_student_data as $o_student) {
                    $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                    $o_sheet->setCellValue('B'.$i_row, $o_student->student_number);
                    $o_sheet->setCellValue('C'.$i_row, $o_student->student_type);
                    $o_sheet->setCellValue('D'.$i_row, $o_student->study_program_abbreviation);
                    $o_sheet->setCellValue('E'.$i_row, $o_student->academic_year_id);

                    $c_semester_student = $c_semester;
                    foreach ($mba_semester_list as $o_semester) {
                        $mba_student_semester_data = $this->General->get_where('dt_student_semester', [
                            'student_id' => $o_student->student_id,
                            'academic_year_id' => $o_semester->academic_year_id,
                            'semester_type_id' => $o_semester->semester_type_id
                        ]);
                        
                        if ($mba_student_semester_data) {
                            $o_sheet->setCellValue($c_semester_student.$i_row, $mba_student_semester_data[0]->student_semester_status);
                        }
                        $c_semester_student++;
                    }

                    $i_row++;
                }
            }
        }

        $i_row_footer = $i_row - 1;

        $o_sheet->setCellValue('A'.$i_row, 'Total Mahasiswa Aktif');
        $o_sheet->mergeCells('A'.$i_row.':E'.$i_row);

        $s_prodi_id = $prodi_data[0]->study_program_id;
        // $c_semester_footer = $c_semester;
        // foreach ($mba_semester_list as $o_semester) {
        //     // $s_periode = $o_semester->academic_year_id.$o_semester->semester_type_id;
        //     // // $a_feeder_count_data = $this->feederapi->post('GetCountAktivitasMahasiswa');
        //     // $a_feeder_count_data = $this->feederapi->post('GetListPerkuliahanMahasiswa', array(
        //     //     'filter' => "id_prodi = '$s_prodi_id' AND id_status_mahasiswa = 'A' AND id_semester = '$s_periode'"
        //     //     // 'filter' => "id_prodi = '$s_prodi_id' AND id_semester = '20171'"
        //     // ));
        //     // print('<pre>');var_dump($a_feeder_count_data->data);exit;
        //     // $s_count = count($a_feeder_count_data->data);
        //     // print($s_count);exit;

        //     // $o_sheet->setCellValue($c_semester_footer.$i_row, "=COUNTIF(".$c_semester_footer."3:".$c_semester_footer.$i_row_footer.";)");
        //     // $row_feeder = $i_row + 1;
        //     // $o_sheet->setCellValue($c_semester_footer.$row_feeder, $s_count);
        //     $c_semester_footer++;
        // }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_file_name);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_file_path.$s_file_name );
        exit;
    }

    public function status_mahasiswa_list()
    {
        $this->load->library('FeederAPI');
        $a_status_allowed = ['active', 'inactive', 'onleave', 'resign', 'graduated', 'dropout'];
        $mba_semester_list = $this->Smm->get_semester_setting(['dss.academic_year_id <= ' => '2022']);
        // $prodi_data_main = $this->General->get_where('ref_study_program', ['study_program_main_id' => $s_prodi_main_id]);
        // $prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_prodi_main_id]);
        // if ($prodi_data_main) {
        //     $prodi_data = array_merge($prodi_data, $prodi_data_main);
        // }
        
        // if (!$prodi_data) {
        //     print('prodi not found!');exit;
        // }
        if (!$mba_semester_list) {
            print('semester not found!');exit;
        }

        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_file_name = 'Student_body.xlsx';
        $s_file_path = APPPATH."uploads/temp/";

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }
        
        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $o_spreadsheet->getProperties()
            ->setTitle($s_file_name)
            ->setCreator("IULI Academic Services");

        $i_row = 1;
        $c_semester = 'F';
        $o_sheet->setCellValue('A'.$i_row, 'Student Name');
        $o_sheet->setCellValue('B'.$i_row, 'Student ID Number');
        $o_sheet->setCellValue('C'.$i_row, 'Student Type');
        $o_sheet->setCellValue('D'.$i_row, 'Prodi');
        $o_sheet->setCellValue('E'.$i_row, 'Batch');
        $o_sheet->setCellValue('F'.$i_row, 'Status per akademik semester');
        $o_sheet->mergeCells('A1:A2');
        $o_sheet->mergeCells('B1:B2');
        $o_sheet->mergeCells('C1:C2');
        $o_sheet->mergeCells('D1:D2');
        $o_sheet->mergeCells('E1:E2');
        $i_row++;

        $c_semester_header = $c_semester;
        foreach ($mba_semester_list as $o_semester) {
            $o_sheet->setCellValue($c_semester_header.$i_row, $o_semester->academic_year_id.$o_semester->semester_type_id);
            $c_semester_header++;
        }
        $c_semester_header = chr(ord($c_semester_header) - 1);
        $o_sheet->mergeCells($c_semester.'1:'.$c_semester_header.'1');

        $i_row++;
        // print('<pre>');var_dump($prodi_data);exit;

        // foreach ($prodi_data as $o_prodi) {
            $mba_student_data = $this->Stm->get_student_filtered(false, $a_status_allowed);

            if ($mba_student_data) {
                foreach ($mba_student_data as $o_student) {
                    $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                    $o_sheet->setCellValue('B'.$i_row, $o_student->student_number);
                    $o_sheet->setCellValue('C'.$i_row, $o_student->student_type);
                    $o_sheet->setCellValue('D'.$i_row, $o_student->study_program_abbreviation);
                    $o_sheet->setCellValue('E'.$i_row, $o_student->academic_year_id);

                    $c_semester_student = $c_semester;
                    foreach ($mba_semester_list as $o_semester) {
                        $mba_student_semester_data = $this->General->get_where('dt_student_semester', [
                            'student_id' => $o_student->student_id,
                            'academic_year_id' => $o_semester->academic_year_id,
                            'semester_type_id' => $o_semester->semester_type_id
                        ]);
                        
                        if ($mba_student_semester_data) {
                            $o_sheet->setCellValue($c_semester_student.$i_row, $mba_student_semester_data[0]->student_semester_status);
                        }
                        $c_semester_student++;
                    }

                    $i_row++;
                }
            }
        // }

        $i_row_footer = $i_row - 1;

        $o_sheet->setCellValue('A'.$i_row, 'Total Mahasiswa Aktif');
        $o_sheet->mergeCells('A'.$i_row.':E'.$i_row);

        // $s_prodi_id = $prodi_data[0]->study_program_id;
        // $c_semester_footer = $c_semester;
        // foreach ($mba_semester_list as $o_semester) {
        //     // $s_periode = $o_semester->academic_year_id.$o_semester->semester_type_id;
        //     // // $a_feeder_count_data = $this->feederapi->post('GetCountAktivitasMahasiswa');
        //     // $a_feeder_count_data = $this->feederapi->post('GetListPerkuliahanMahasiswa', array(
        //     //     'filter' => "id_prodi = '$s_prodi_id' AND id_status_mahasiswa = 'A' AND id_semester = '$s_periode'"
        //     //     // 'filter' => "id_prodi = '$s_prodi_id' AND id_semester = '20171'"
        //     // ));
        //     // print('<pre>');var_dump($a_feeder_count_data->data);exit;
        //     // $s_count = count($a_feeder_count_data->data);
        //     // print($s_count);exit;

        //     // $o_sheet->setCellValue($c_semester_footer.$i_row, "=COUNTIF(".$c_semester_footer."3:".$c_semester_footer.$i_row_footer.";)");
        //     // $row_feeder = $i_row + 1;
        //     // $o_sheet->setCellValue($c_semester_footer.$row_feeder, $s_count);
        //     $c_semester_footer++;
        // }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_file_name);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_file_path.$s_file_name );
        exit;
    }

    public function repair_employee_data()
    {
        $this->load->model('employee/Employee_model', 'Emm');
        $s_template_path = APPPATH.'uploads/temp/employee_database.xlsx';
        // $s_file_name = 'student_graduation_2021_1.xlsx';
        // $s_file_path = APPPATH."uploads/temp/";
        // if(!file_exists($s_file_path)){
        //     mkdir($s_file_path, 0777, TRUE);
        // }

		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $i_row = 2;
        while ($o_sheet->getCell('A'.$i_row)->getValue() !== NULL) {
            $s_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('C'.$i_row)->getValue()));
            $s_nip = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('A'.$i_row)->getValue()));
            $s_nidn = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('B'.$i_row)->getValue()));
            $s_status = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('F'.$i_row)->getValue()));
            $s_job_title = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('G'.$i_row)->getValue())));
            $s_homebase = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('H'.$i_row)->getValue())));
            $s_homebase_status = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('N'.$i_row)->getValue())));
            $s_category_academic = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('O'.$i_row)->getValue())));
            $s_join_date = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('K'.$i_row)->getValue())));
            $s_join_date = (!empty($s_join_date)) ? date('Y-m-d', strtotime($s_join_date)) : NULL;
            // print($s_name.' '.$s_join_date);exit;
            $s_last_date_work = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('T'.$i_row)->getValue())));
            $s_last_date_work = (!empty($s_last_date_work)) ? date('Y-m-d', strtotime($s_last_date_work)) : NULL;
            $s_last_date_employment = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('S'.$i_row)->getValue())));
            $s_last_date_employment = (!empty($s_last_date_employment)) ? date('Y-m-d', strtotime($s_last_date_employment)) : NULL;
            $s_dob = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AC'.$i_row)->getValue())));
            $s_dob = (!empty($s_dob)) ? date('Y-m-d', strtotime($s_dob)) : NULL;
            $s_academic_rank = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('Q'.$i_row)->getValue())));
            $s_resign_note = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('U'.$i_row)->getValue())));
            $s_email = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('V'.$i_row)->getValue())));
            $s_phone = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('W'.$i_row)->getValue())));
            $s_employee_leave_allowance = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('X'.$i_row)->getValue())));
            $s_employee_working_hour_status = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('Y'.$i_row)->getValue())));
            $s_place_of_birth = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AB'.$i_row)->getValue())));
            $s_nik = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AL'.$i_row)->getValue())));
            $s_blood = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AR'.$i_row)->getValue())));
            $s_npwp = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AS'.$i_row)->getValue())));
            $s_mother_name = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AU'.$i_row)->getValue())));
            $s_nationality = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AJ'.$i_row)->getValue())));
            $s_religion = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AH'.$i_row)->getValue())));
            $s_address = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AN'.$i_row)->getValue())));
            $s_address_province = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AQ'.$i_row)->getValue())));
            $s_personal_email = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AK'.$i_row)->getValue())));
            $s_employee_pkpt = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AA'.$i_row)->getValue())));
            $s_employee_dept = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('BA'.$i_row)->getValue())));
            $s_gender = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AF'.$i_row)->getValue())));
            $s_bank_code = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('BB'.$i_row)->getValue())));
            $s_account_bank = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AE'.$i_row)->getValue())));
            $s_account_holder = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AG'.$i_row)->getValue())));
            $s_personal_hp = trim(str_replace('"', '', str_replace('=', '', $o_sheet->getCell('AI'.$i_row)->getValue())));
            $a_personal_hp = explode('/', str_replace(' ', '', $s_personal_hp));
            $s_personal_hp_1 = $a_personal_hp[0];
            $s_personal_hp_2 = (count($a_personal_hp) > 1) ? $a_personal_hp[1] : NULL;
            // var_dump($s_personal_hp_2);
            // print('<br>');

            $mba_employee_data = $this->Emm->get_employee_data([
                'employee_id_number' => $s_nip,
                // 'personal_data_name' => $s_name
            ]);

            if ($mba_employee_data) {
                $o_employee = $mba_employee_data[0];
                // $s_db_email = strtolower($o_employee->employee_email);
                // $s_excel_email = strtolower($s_email);
                // if ($s_db_email != $s_excel_email) {
                //     if (!empty($s_excel_email)) {
                //         print($o_employee->employee_email.' - '.$s_email.'/'.$o_employee->employee_id);
                //         print('<br>');
                //     }
                // }
                // if ($o_employee->personal_data_id == '6d2c9f65-0f6a-4fc2-8a26-61a204d5b156') {
                //     print($s_name.' '.$s_place_of_birth);exit;
                // }
                $a_employee_data = [
                    'employee_job_title' => (!empty($s_job_title)) ? $s_job_title : NULL,
                    'employee_join_date' => $s_join_date,
                    'employee_academic_rank' => (!empty($s_academic_rank)) ? $s_academic_rank : NULL,
                    'employment_group' => ($s_category_academic == 'Academic') ? 'ACADEMIC' : 'NONACADEMIC',
                    'last_date_of_work' => $s_last_date_work,
                    'last_date_of_employment' => $s_last_date_employment,
                    'employee_resignation_reason' => (!empty($s_resign_note)) ? $s_resign_note : NULL,
                    'employee_phone' => (!empty($s_phone)) ? $s_phone : NULL,
                    'employee_leave_allowance' => (!empty($s_employee_leave_allowance)) ? $s_employee_leave_allowance : 12,
                    'employee_pkpt' => (!empty($s_employee_pkpt)) ? $s_employee_pkpt : NULL,
                    // 'department_id' => (empty($s_employee_dept)) ? NULL : $s_employee_dept
                ];

                $a_personal_data = [
                    'personal_data_id_card_number' => (!empty($s_nik)) ? $s_nik : NULL,
                    'personal_data_id_card_type' => (!empty($s_nik)) ? 'national_id' : NULL,
                    'personal_data_place_of_birth' => (!empty($s_place_of_birth)) ? $s_place_of_birth : NULL,
                    'personal_data_date_of_birth' => $s_dob,
                    'personal_data_blood_group' => (!empty($s_blood)) ? $s_blood : NULL,
                    'personal_data_npwp_number' => (!empty($s_npwp)) ? $s_npwp : NULL,
                    'personal_data_mother_maiden_name' => (!empty($s_mother_name)) ? $s_mother_name : NULL,
                    'personal_data_gender' => (!empty($s_gender)) ? $s_gender : NULL,
                    'personal_data_nationality' => (!empty($s_nationality)) ? $s_nationality : NULL,
                ];
                
                $a_account_data = [
                    'employee_id' => $o_employee->employee_id,
                    'bank_code' => (!empty($s_bank_code)) ? $s_bank_code : NULL,
                    'account_number' => (!empty($s_account_bank)) ? $s_account_bank : NULL,
                    'account_holder' => (!empty($s_account_holder)) ? $s_account_holder : NULL,
                ];

                if ((!empty($s_bank_code)) AND (!empty($s_account_bank)) AND (!empty($s_account_holder))) {
                    $mba_employee_account = $this->Emm->get_employee_account_bank($o_employee->employee_id);
                    if ($mba_employee_account) {
                        $s_account_id = $mba_employee_account[0]->account_id;
                        $this->Emm->submit_employee_account($a_account_data, ['account_id' => $s_account_id]);
                    }
                    else {
                        $a_account_data['account_id'] = $this->uuid->v4();
                        $this->Emm->submit_employee_account($a_account_data);
                    }
                }
                
                if (!empty($s_address)) {
                    $mba_dt_personal_address = $this->General->get_where('dt_personal_address', ['personal_data_id' => $o_employee->personal_data_id]);
                    if ($mba_dt_personal_address) {
                        $a_personal_address = [
                            'personal_address_text' => $s_address.' '.$s_address_province
                        ];

                        $this->db->update('dt_personal_address', $a_personal_address, ['personal_data_id' => $o_employee->personal_data_id, 'address_id' => $mba_dt_personal_address[0]->address_id]);
                    }
                    else {
                        $a_personal_address = [
                            'personal_data_id' => $o_employee->personal_data_id,
                            'address_id' => NULL,
                            'personal_address_name' => NULL,
                            'personal_address_text' => $s_address.' '.$s_address_province,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('dt_personal_address', $a_personal_address);
                    }
                }

                $a_personal_email = explode('/', $s_personal_email);
                if (count($a_personal_email) > 0) {
                    foreach ($a_personal_email as $s_email_personal) {
                        $s_email_personal = trim($s_email_personal);
                        $s_email_personal = str_replace(' ', '', $s_email_personal);
                        if ($s_email_personal != $o_employee->personal_data_email) {
                            $a_personal_contact = [
                                'personal_data_id' => $o_employee->personal_data_id,
                                'contact_type' => 'email',
                                'contact_fill' => $s_email_personal
                            ];

                            $mba_personal_contact = $this->General->get_where('dt_personal_data_contact', [
                                'personal_data_id' => $o_employee->personal_data_id,
                                'contact_fill' => $s_email_personal
                            ]);

                            if ($mba_personal_contact) {
                                $this->Pdm->submit_personal_contact($a_personal_contact, ['personal_contact_id' => $mba_personal_contact[0]->personal_contact_id]);
                            }
                            else {
                                $a_personal_contact['personal_contact_id'] = $this->uuid->v4();
                                $this->Pdm->submit_personal_contact($a_personal_contact);
                            }
                        }
                    }
                }
                
                switch ($s_employee_working_hour_status) {
                    case 'Full Time':
                        $a_employee_data['employee_working_hour_status'] = 'Full Time';
                        break;

                    case 'Part Time':
                        $a_employee_data['employee_working_hour_status'] = 'Part Time';
                        break;

                    case 'Semi Full Time':
                        $a_employee_data['employee_working_hour_status'] = 'Semi Full Time';
                        break;
                    
                    default:
                        $a_employee_data['employee_working_hour_status'] = 'Full Time';
                        break;
                }

                switch ($s_religion) {
                    case 'Islam':
                        $a_personal_data['religion_id'] = '53b17ff0-e4c0-4fc9-8735-bbb8c7054048';
                        break;

                    case 'Katholik':
                        $a_personal_data['religion_id'] = 'd5c8f0fd-fdb0-4dfa-8e2f-e863d96e98cd';
                        break;

                    case 'Kristen':
                        $a_personal_data['religion_id'] = 'e703430a-e6bc-491b-8d75-75024ed80551';
                        break;

                    case 'Hindu':
                        $a_personal_data['religion_id'] = '223e769b-cc54-48e4-8446-574377083120';
                        break;

                    case 'Budha':
                        $a_personal_data['religion_id'] = 'fc389367-54a8-42a4-99bc-7ffa2d1a3e42';
                        break;

                    case 'Protestan':
                        $a_personal_data['religion_id'] = 'e703430a-e6bc-491b-8d75-75024ed80551';
                        break;

                    default:
                        $a_personal_data['religion_id'] = NULL;
                        break;
                }

                switch ($s_homebase_status) {
                    case 'Homebase':
                        $a_employee_data['employee_homebase_status'] = 'homebase';
                        break;

                    case 'Non Homebase':
                        $a_employee_data['employee_homebase_status'] = 'non-homebase';
                        break;
                    
                    default:
                        $a_employee_data['employee_homebase_status'] = 'non-homebase';
                        break;
                }

                if (!empty($s_nidn)) {
                    $a_employee_data['employee_lecturer_number'] = $s_nidn;
                    $a_employee_data['employee_lecturer_number_type'] = 'NIDN';
                    $a_employee_data['employee_lecturer_is_reported'] = 'TRUE';
                }

                $s_status = strtolower($s_status);
                switch ($s_status) {
                    case 'active':
                        $a_employee_data['status'] = 'ACTIVE';
                        break;

                    case 'resign':
                        $a_employee_data['status'] = 'RESIGN';
                        break;

                    case 'in active':
                        $a_employee_data['status'] = 'RESIGN';
                        break;

                    case 'part time':
                        $a_employee_data['status'] = 'PART TIME';
                        break;

                    case 'Study Permit':
                        $a_employee_data['status'] = 'STUDY PERMIT';
                        break;
                    
                    default:
                        $a_employee_data['status'] = 'IN ACTIVE';
                        break;
                }

                $this->Pdm->update_personal_data($a_personal_data, $o_employee->personal_data_id);
                $this->Emm->update_empoyee_param($a_employee_data, ['employee_id' => $o_employee->employee_id]);
            }
            
            $i_row++;
        }
        exit;
    }

    function graduated_student() {
        $a_student_id = ['65aec8e9-3287-43a4-9ed0-d06f156d604d','7e4ce0ba-f0e5-4173-94c9-ec12d6b88a74','3aff8ddd-242a-49d3-8543-2c70a30ec6af','c18f818e-4004-41c5-bec5-9a548e0c5ddd','5c438173-124e-46a7-8126-50a3f6eb439a','dcad1e92-a6f8-4b89-a3ca-fff7c0d5d157'];
        foreach ($a_student_id as $s_student_id) {
            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
            if ($mba_student_data) {
                $o_student_data = $mba_student_data[0];
            }
            else {
                print($s_student_id.' not found!');exit;
            }
            
            $d_total_unpaid = 0;
            $mba_invoice_student = $this->Im->get_unpaid_invoice_full([
                'di.personal_data_id' => $o_student_data->personal_data_id
            ]);
            if ($mba_invoice_student) {
                foreach ($mba_invoice_student as $o_invoice) {
                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                    $mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                    if ($mba_invoice_installment) {
                        foreach ($mba_invoice_installment as $o_installment) {
                            if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                $d_total_unpaid += $o_installment->sub_invoice_details_amount_total;
                            }
                        }
                    }
                    else if ($mbo_invoice_fullpayment) {
                        $d_total_unpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                    }
                }
            }

            print($o_student_data->personal_data_name.': '.$d_total_unpaid);
            print('<br>');
        }
    }

    public function retrieve_student_thesis()
    {
        $this->load->model('thesis/Thesis_model', 'Tm');
        $mba_student_graduate = $this->Stm->get_student_filtered([
            'ds.student_status' => 'graduated'
        ]);

        if ($mba_student_graduate) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'student_graduation.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IST Services")
                ->setCategory("From System");

            $i_row = 1;
            $o_sheet->setCellValue('A'.$i_row, 'Name');
            $o_sheet->setCellValue('B'.$i_row, 'Batch');
            $o_sheet->setCellValue('C'.$i_row, 'Graduation Year');
            $o_sheet->setCellValue('D'.$i_row, 'Prodi');
            $o_sheet->setCellValue('E'.$i_row, 'Thesis Title');
            $o_sheet->setCellValue('F'.$i_row, 'Advisor 1');
            $o_sheet->setCellValue('G'.$i_row, 'Advisor 2');
            $o_sheet->setCellValue('H'.$i_row, 'Advisor 3');
            $o_sheet->setCellValue('I'.$i_row, 'Examiner 1');
            $o_sheet->setCellValue('J'.$i_row, 'Examiner 2');
            $o_sheet->setCellValue('K'.$i_row, 'Examiner 3');

            $i_row++;
            $a_data = [];
            $i_max_advisor = 0;
            $i_max_examiner = 0;
            $c_advisor = 'F';
            $c_examiner = 'I';
            foreach ($mba_student_graduate as $o_student) {
                $mba_student_thesis = $this->Tm->get_thesis_log_student(['ts.student_id' => $o_student->student_id]);
                $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('B'.$i_row, $o_student->academic_year_id);
                $o_sheet->setCellValue('C'.$i_row, $o_student->graduated_year_id);
                $o_sheet->setCellValue('D'.$i_row, $o_student->study_program_name);
                
                if ($mba_student_thesis) {
                    $o_student_thesis = $mba_student_thesis[0];
                    $mba_student_advisor = $this->Tm->get_advisor_student($o_student_thesis->thesis_student_id);
                    $mba_student_examiner = $this->Tm->get_examiner_student($o_student_thesis->thesis_student_id);
                    $o_sheet->setCellValue('E'.$i_row, $o_student_thesis->thesis_title);
                    
                    $a_advisor_name = [];
                    $c_start_advisor = $c_advisor;
                    $c_start_examiner = $c_examiner;

                    if ($mba_student_advisor) {
                        $count_advisor = count($mba_student_advisor);
                        $i_max_advisor = ($count_advisor > $i_max_advisor) ? $count_advisor : $i_max_advisor;
                        foreach ($mba_student_advisor as $o_advisor) {
                            $s_advisor_name = $this->General->retrieve_title($o_advisor->personal_data_id);
                            $o_sheet->setCellValue($c_start_advisor++.$i_row, $s_advisor_name);
                        }
                    }

                    $a_examiner_name = [];
                    if ($mba_student_examiner) {
                        $count_examiner = count($mba_student_examiner);
                        $i_max_examiner = ($count_examiner > $i_max_examiner) ? $count_examiner : $i_max_examiner;
                        foreach ($mba_student_examiner as $o_examiner) {
                            $s_examiner_name = $this->General->retrieve_title($o_examiner->personal_data_id);
                            $o_sheet->setCellValue($c_start_examiner++.$i_row, $s_examiner_name);
                        }
                    }

                    

                    // array_push($a_data, $a_data_send);
                }
                
                $i_row++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

        }
    }

    public function retrieve_old_thesis()
    {
        $this->load->model('thesis_old_model', 'Tom');
        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0D0D'),
            ));
        
        $s_template_path = APPPATH.'uploads/temp/student_graduation.xlsx';
        $s_file_name = 'student_graduation_result.xlsx';
        $s_file_path = APPPATH."uploads/temp/";

        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();

        for ($i_row=84; $i_row <= 233; $i_row++) { 
            $s_student_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('A'.$i_row)->getValue()));
            
            $mbo_defense_data = $this->Tom->get_from_defense($s_student_name);

            if ($mbo_defense_data) {
                $s_advisor_1 = $mbo_defense_data->advisor;
                $s_advisor_2 = $mbo_defense_data->co_advisor;
                $s_examiner_1 = $mbo_defense_data->examiner_1;
                $s_examiner_2 = $mbo_defense_data->examiner_2;
                $s_examiner_3 = $mbo_defense_data->examiner_3;

                $o_sheet->setCellValue('F'.$i_row, $s_advisor_1);
                $o_sheet->setCellValue('G'.$i_row, $s_advisor_2);
                $o_sheet->setCellValue('I'.$i_row, $s_examiner_1);
                $o_sheet->setCellValue('J'.$i_row, $s_examiner_2);
                $o_sheet->setCellValue('K'.$i_row, $s_examiner_3);

                // $o_sheet->getStyle('F'.$i_row.':K'.$i_row)->getFill()
                //     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //     ->getStartColor()->setARGB('40BA30');
                $o_sheet->getStyle('F'.$i_row.':K'.$i_row)->applyFromArray($styleArray);
            }
            // print('<pre>');var_dump($mba_defense_data);exit;
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);
    }

    public function get_tracer_result()
    {
        $this->load->model('alumni/Alumni_model', 'Alm');
        $mba_student_graduate = $this->Stm->get_student_filtered([
            'ds.student_status' => 'graduated'
        ]);
        if ($mba_student_graduate) {
            $path_master = APPPATH."uploads/temp/";
            $s_file = 'student_graduation_tracer2';
            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');

            fputcsv($fp, [
                'student_name',
                'student_number',
                'batch',
                'graduation_year',
                'study_program',
                'faculty',
                'question',
                'question_answer',
                'question_value',
                'answer_other',
            ]);

            $a_question_id = ['f5','f8','iuli1','f11'];
            foreach ($mba_student_graduate as $o_student) {
                $mba_has_filled_tracer_dikti = $this->General->get_where('dikti_question_answers', ['personal_data_id' => $o_student->personal_data_id]);
                if ($mba_has_filled_tracer_dikti) {
                    foreach ($a_question_id as $s_question_id) {
                        $answer_question = $this->Alm->get_question_answer([
                            'dq.question_id' => $s_question_id,
                            'dqa.personal_data_id' => $o_student->personal_data_id
                        ]);
                        $mba_question_data = $this->General->get_where('dikti_questions', ['question_id' => $s_question_id]);

                        if ($answer_question) {
                            foreach ($answer_question as $o_questionanswer) {
                                fputcsv($fp, [
                                    $o_student->personal_data_name,
                                    $o_student->student_number,
                                    $o_student->academic_year_id,
                                    $o_student->graduated_year_id,
                                    $o_student->study_program_abbreviation,
                                    $o_student->faculty_abbreviation,
                                    $mba_question_data[0]->question_name,
                                    $o_questionanswer->question_choice_name,
                                    $o_questionanswer->question_choice_value,
                                    $o_questionanswer->answer_content
                                ]);
                            }
                        }
                        else {
                            fputcsv($fp, [
                                $o_student->personal_data_name,
                                $o_student->student_number,
                                $o_student->academic_year_id,
                                $o_student->graduated_year_id,
                                $o_student->study_program_abbreviation,
                                $o_student->faculty_abbreviation,
                                $mba_question_data[0]->question_name,
                                '',
                                '',
                                ''
                            ]);
                        }
                    }
                }
            }

            print('oke');
        }
    }

    // public function repair_candidate()
    // {
    //     $a_personal_data_required = ['personal_data_name', 'personal_data_email', 'personal_data_cellular', 'personal_data_id_card_number', 'personal_data_place_of_birth', 'personal_data_date_of_birth', 'personal_data_gender', 'personal_data_nationality', 'personal_data_mother_maiden_name', 'country_of_birth', 'citizenship_id', 'religion_id'];
    //     $a_personal_data_family_required = ['personal_data_name', 'family_member_status', 'personal_data_email', 'personal_data_cellular'];
    //     $a_academic_history_required = ['study_program_id', 'institution_id', 'academic_history_id', 'academic_history_graduation_year'];

    //     $d_count_complete = 0;
    //     $d_count_uncomplete = 0;
    //     $mba_candidate_data = $this->General->get_where('dt_student', ['student_status' => 'candidate']);
    //     if ($mba_candidate_data) {
    //         foreach ($mba_candidate_data as $o_student_candidate) {
    //             $b_personal_data_complete = true;
    //             $b_parent_complete = true;
    //             $b_school_complete = true;

    //             $mba_candidate_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_student_candidate->personal_data_id]);
    //             $mba_candidate_family_data = $this->General->get_where('dt_family_member', ['personal_data_id' => $o_student_candidate->personal_data_id]);
    //             $mbo_candidate_academic_history = $this->Dm->get_candidate_academic_history($o_student_candidate->personal_data_id);
                
    //             if (!$mba_candidate_family_data) {
    //                 $b_parent_complete = false;
    //             }
    //             else {
    //                 $mbo_candidate_parent = $this->Dm->get_student_parent($mba_candidate_family_data[0]->family_id);
    //                 if ($mbo_candidate_parent) {
    //                     foreach ($a_personal_data_family_required as $s_field_pr) {
    //                         if (is_null($mbo_candidate_parent->$s_field_pr)) {
    //                             $b_parent_complete = false;
    //                         }
    //                     }
    //                 }
    //             }

    //             if (!$mba_candidate_personal_data) {
    //                 print('student_id personal_data table not found!'.$o_student_candidate->student_id);exit;
    //             }
    //             $o_personal_data_candidate = $mba_candidate_personal_data[0];

    //             foreach ($a_personal_data_required as $s_field_pd) {
    //                 if (is_null($o_personal_data_candidate->$s_field_pd)) {
    //                     $b_personal_data_complete = false;
    //                 }
    //             }

    //             if ($mbo_candidate_academic_history) {
    //                 foreach ($a_academic_history_required as $s_field_ah) {
    //                     if (is_null($mbo_candidate_academic_history->$s_field_ah)) {
    //                         $b_school_complete = false;
    //                     }
    //                 }
    //             }

    //             if (($b_personal_data_complete) AND ($b_parent_complete) AND ($b_school_complete)) {
    //                 $update_complete_data = $this->General->update_data('dt_personal_data', [
    //                     'has_completed_personal_data' => '0',
    //                     'has_completed_parents_data' => '0',
    //                     'has_completed_school_data' => '0'
    //                 ], ['personal_data_id' => $o_student_candidate->personal_data_id]);

    //                 print($o_personal_data_candidate->personal_data_name.' complete!');
    //                 $d_count_complete++;
    //             }
    //             else {
    //                 $update_student_register = $this->General->update_data('dt_student', ['student_status' => 'register'], ['student_id' => $o_student_candidate->student_id]);
    //                 print($o_personal_data_candidate->personal_data_name.' belum komplit!');
    //                 $d_count_uncomplete++;
    //             }
    //             print('<br>');
    //         }
    //     }

    //     print('<h3>'.$d_count_complete.' Sudah melengkapi data</h3>');
    //     print('<h3>'.$d_count_uncomplete.' Belum melengkapi data</h3>');
    // }

    function get_internship_subject() {
        $s_subject_name = 'internship';
        $a_semester_type_allowed = [1,2];
        $mba_student_subject_data = $this->Scm->get_studentscore_like_subject_name([
            'sc.score_approval' => 'approved',
            'sc.semester_id != ' => '17',
			'sc.score_display' => 'TRUE'
        ], $s_subject_name);

        if ($mba_student_subject_data) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'student_internship.xlsx';
            $s_file_path = APPPATH."uploads/temp/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI IT Services")
                ->setCategory("Student Subject");

            $i_row = 1;
            $o_sheet->setCellValue('A'.$i_row, 'Student Name');
            $o_sheet->setCellValue('B'.$i_row, 'Student Number');
            $o_sheet->setCellValue('C'.$i_row, 'Study Program');
            $o_sheet->setCellValue('D'.$i_row, 'Faculty');
            $o_sheet->setCellValue('E'.$i_row, 'Academic Semester');
            $o_sheet->setCellValue('F'.$i_row, 'Score Total');
            $o_sheet->setCellValue('G'.$i_row, 'Grade Point');
            $o_sheet->setCellValue('H'.$i_row, 'Grade');
            $i_row++;

            foreach ($mba_student_subject_data as $o_student) {
                if (in_array($o_student->semester_type_id, $a_semester_type_allowed)) {
                    $o_student_data = $this->Stm->get_student_filtered([
                        'ds.student_id' => $o_student->student_id
                    ])[0];
                    $mba_subject_internship = $this->Scm->get_score_like_subject_name([
                        'sc.student_id' => $o_student->student_id,
                        'sc.score_approval' => 'approved',
                        'sc.semester_id != ' => '17',
			            'sc.score_display' => 'TRUE'
                    ], $s_subject_name, $s_ordering = 'DESC');
    
                    if (($mba_subject_internship) AND (count($mba_subject_internship) == 1)) {
                        $d_gp = $this->grades->get_grade_point($mba_subject_internship[0]->score_sum);
                        $grade = $this->grades->get_grade($mba_subject_internship[0]->score_sum);
                        $o_sheet->setCellValue('A'.$i_row, $o_student_data->personal_data_name);
                        $o_sheet->setCellValue('B'.$i_row, $o_student_data->student_number);
                        $o_sheet->setCellValue('C'.$i_row, $o_student_data->study_program_name_feeder);
                        $o_sheet->setCellValue('D'.$i_row, $o_student_data->faculty_abbreviation);
                        $o_sheet->setCellValue('E'.$i_row, $o_student->academic_year_id.'-'.$o_student->semester_type_id);
                        $o_sheet->setCellValue('F'.$i_row, $mba_subject_internship[0]->score_sum);
                        $o_sheet->setCellValue('G'.$i_row, $d_gp);
                        $o_sheet->setCellValue('H'.$i_row, $grade);
                        $o_sheet->setCellValue('I'.$i_row, $mba_subject_internship[0]->subject_name);
                        $o_sheet->setCellValue('J'.$i_row, 'single');
    
                        $i_row++;
                    }
                    else if ($mba_subject_internship) {
                        foreach ($mba_subject_internship as $o_score) {
                            if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                                $d_gp = $this->grades->get_grade_point($o_score->score_sum);
                                $grade = $this->grades->get_grade($o_score->score_sum);
                                $o_sheet->setCellValue('A'.$i_row, $o_student_data->personal_data_name);
                                $o_sheet->setCellValue('B'.$i_row, $o_student_data->student_number);
                                $o_sheet->setCellValue('C'.$i_row, $o_student_data->study_program_name_feeder);
                                $o_sheet->setCellValue('D'.$i_row, $o_student_data->faculty_abbreviation);
                                $o_sheet->setCellValue('E'.$i_row, $o_student->academic_year_id.'-'.$o_student->semester_type_id);
                                $o_sheet->setCellValue('F'.$i_row, $o_score->score_sum);
                                $o_sheet->setCellValue('G'.$i_row, $d_gp);
                                $o_sheet->setCellValue('H'.$i_row, $grade);
                                $o_sheet->setCellValue('I'.$i_row, $o_score->subject_name);
    
                                $i_row++;
                            }
                        }
                    }
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_file_name);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_file_name);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
            readfile( $s_file_path.$s_file_name );
            exit;
        }
        // print('<pre>');var_dump($mba_student_subject_data);exit;
    }

    // function testinggg() {
    //     $a_va_number = ['8310112207001002', '8310112210003002', '8310112102015002', '8310112209003002', '8310112101012002', '8310112209006002', '8310112201011002', '8310112102010002', '8310112102008002', '8310112102012002', '8310112207005002', '8310112102014002', '8310112102005002', '8310112106006002'];
    //     foreach ($a_va_number as $s_va_number) {
    //         echo modules::run('finance/invoice/activated_virtual_account', $s_va_number);
    //     }
    // }

    function collective_repair() {
        $a_student_number = ['11202201009','11202201005','11202202009','11202202001','11202206002','11202212004','11202205002','11202204002','11202204004','11202202007','11202307002','11202309001','11202301005'];
        $mba_list_student = $this->General->get_in('dt_student', 'student_number', $a_student_number, ['student_status != ' => 'resign']);
        if ($mba_list_student) {
            foreach ($mba_list_student as $o_student) {
                $unpaid_invoice = $this->Im->get_unpaid_invoice_full(['di.personal_data_id' => $o_student->personal_data_id], ['02']);
                if (($unpaid_invoice) AND (count($unpaid_invoice) == 1)) {
                    print('<br><h4>'.$o_student->student_email.'</h4>');
                    $s_va_number = $this->force_repair_invoice($unpaid_invoice[0]->invoice_id, true);
                    echo modules::run('finance/invoice/activated_virtual_account', $s_va_number);
                }
            }
        }
        // print('<pre>');var_dump($mba_list_student);exit;
    }

    function generate_va_new($s_student_id, $s_payment_code = '02') {
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.student_id' => $s_student_id,
        ]);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            $s_va_number = $this->Bnim->generate_va_number(
                $s_payment_code,
                'student',
                $o_student->student_number,
                $o_student->finance_year_id,
                $o_student->program_id
            );
            print($s_va_number);
        }
        else {
            print('zoonk');
        }
    }

    function force_repair_invoice($s_invoice_id, $b_get_va = false) {
        $current_time = date('Y-m-d H:i:s');
        $mba_invoice_data = $this->Im->get_invoice_list_detail(['di.invoice_id' => $s_invoice_id]);
        if ($mba_invoice_data) {
            // print('<pre>');var_dump($mba_invoice_data);exit;
            $s_payment_type = $mba_invoice_data[0]->payment_type_code;
            $s_payment_type = (in_array($s_payment_type, ['02', '05'])) ? '02' : $s_payment_type;
            // cancel transaction
            $installment_invoice = $this->Im->get_invoice_installment($s_invoice_id);
            $full_payment_invoice = $this->Im->get_invoice_full_payment($s_invoice_id);

            $mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $full_payment_invoice->personal_data_id, 'student_status != ' => 'resign']);
            if (!$mba_student_data) {
                print('student not found!');exit;
            }

            $o_student = $mba_student_data[0];
            $s_va_number = $this->Bnim->generate_va_number(
                $s_payment_type,
                'student',
                $o_student->student_number,
                $o_student->finance_year_id,
                $o_student->program_id
            );
            if ($full_payment_invoice) {
                if (!is_null($full_payment_invoice->trx_id)) {
                    $check = $this->Bnim->inquiry_billing($full_payment_invoice->trx_id, true);
                    if (($check) AND (!array_key_exists('status', $check))) {
                        if ($check['va_status'] == '1') {
                            $a_update_billing = array(
                                'trx_id' => $full_payment_invoice->trx_id,
                                'trx_amount' => 1,
                                'customer_name' => 'CANCEL PAYMENT',
                                'datetime_expired' => '2020-01-01 23:59:59',
                                'description' => 'CANCEL PAYMENT'
                            );
                    
                            $update = $this->Bnim->update_billing($a_update_billing);
                            if ($update['status'] !== '000') {
                                print('<pre>');
                                var_dump($update);
                            }
                            else {
                                print($full_payment_invoice->trx_id.' cancelled!<br>');
                            }
                        }
                    }

                    $this->Im->update_sub_invoice_details(['trx_id' => NULL], ['sub_invoice_details_id' => $full_payment_invoice->sub_invoice_details_id]);
                }
            }
            if ($installment_invoice) {
                foreach ($installment_invoice as $o_installment) {
                    if (!is_null($o_installment->trx_id)) {
                        $check = $this->Bnim->inquiry_billing($o_installment->trx_id, true);
                        if (($check) AND (!array_key_exists('status', $check))) {
                            if ($check['va_status'] == '1') {
                                $a_update_billing = array(
                                    'trx_id' => $o_installment->trx_id,
                                    'trx_amount' => 1,
                                    'customer_name' => 'CANCEL PAYMENT',
                                    'datetime_expired' => '2020-01-01 23:59:59',
                                    'description' => 'CANCEL PAYMENT'
                                );
                        
                                $update = $this->Bnim->update_billing($a_update_billing);
                                if ($update['status'] !== '000') {
                                    print('<pre>');
                                    var_dump($update);
                                }
                                else {
                                    print($o_installment->trx_id.' cancelled!<br>');
                                }
                            }
                        }
        
                        $this->Im->update_sub_invoice_details(['trx_id' => NULL], ['sub_invoice_details_id' => $o_installment->sub_invoice_details_id]);
                    }
                }
            }

            $s_new_va_number = '<a href="'.base_url().'finance/invoice/activated_virtual_account/'.$s_va_number.'" target="blank">'.$s_va_number.'</a>';

            if (($full_payment_invoice) AND ($full_payment_invoice->sub_invoice_details_status != 'paid')) {
                $this->Im->update_sub_invoice_details(['sub_invoice_details_va_number' => $s_va_number], ['sub_invoice_details_id' => $full_payment_invoice->sub_invoice_details_id]);
                print('change '.$full_payment_invoice->sub_invoice_details_va_number.' to '.$s_new_va_number);print('<br>');
            }
            if ($installment_invoice) {
                foreach ($installment_invoice as $o_installment) {
                    if ($o_installment->sub_invoice_details_status != 'paid') {
                        $this->Im->update_sub_invoice_details(['sub_invoice_details_va_number' => $s_va_number], ['sub_invoice_details_id' => $o_installment->sub_invoice_details_id]);
                        print('change '.$o_installment->sub_invoice_details_va_number.' to '.$s_new_va_number);
                        print('<br>');
                    }
                }
            }

            if ($b_get_va) {
                return $s_va_number;
            }

            // modules::run('finance/invoice/')
        }
    }

    function retrieve_all_student_billing() {
        $mba_student_list = $this->Stm->get_student_filtered(false, ['active', 'graduated']);
        // print('<pre>');var_dump($mba_student_list);exit;
        if ($mba_student_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Unpaid_billing';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/temp/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet()->setTitle("Tuition Fee Report");
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Fee");
            $o_sheet->setCellValue('C1', "Virtual Account");
            $o_sheet->setCellValue('D1', "Total Unpaid");
            $o_sheet->setCellValue('E1', "Invoice ID");

            $i_row = 2;
            foreach ($mba_student_list as $o_student) {
                $billing_fee = $this->Dm->retrieve_all_student_billing([
                    'di.personal_data_id' => $o_student->personal_data_id
                ], 'fee.payment_type_code');
                if ($billing_fee) {
                    // print('<pre>');var_dump($billing_fee);exit;
                    foreach ($billing_fee as $billing_payment_type) {
                        $mba_unpaid_invoice_payment = $this->Im->get_invoice_by_deadline([
                            'di.personal_data_id' => $o_student->personal_data_id,
                            'fee.payment_type_code' => $billing_payment_type->payment_type_code
                        ]);
                        if ($mba_unpaid_invoice_payment) {
                            foreach ($mba_unpaid_invoice_payment as $o_invoice) {
                                $a_va_number = [];
                                $d_total_unpaid = 0;
                                $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                                $mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                                if ($mba_invoice_installment) {
                                    foreach ($mba_invoice_installment as $o_installment) {
                                        if ($o_installment->sub_invoice_details_status != 'paid') {
                                            $d_total_unpaid += $o_installment->sub_invoice_details_amount_total;

                                            if (!in_array($o_installment->sub_invoice_details_va_number, $a_va_number)) {
                                                array_push($a_va_number, $o_installment->sub_invoice_details_va_number);
                                            }
                                        }
                                    }
                                }
                                else if ($mbo_invoice_full_payment) {
                                    $d_total_unpaid += $mbo_invoice_full_payment->sub_invoice_details_amount_total;
                                    if (!in_array($mbo_invoice_full_payment->sub_invoice_details_va_number, $a_va_number)) {
                                        array_push($a_va_number, $mbo_invoice_full_payment->sub_invoice_details_va_number);
                                    }
                                }

                                $o_sheet->setCellValue('A'.$i_row, $o_student->personal_data_name);
                                $o_sheet->setCellValue('B'.$i_row, $o_invoice->fee_description);
                                $o_sheet->setCellValue('C'.$i_row, '="'.implode('|', $a_va_number).'"');
                                $o_sheet->setCellValue('D'.$i_row, $d_total_unpaid);
                                $o_sheet->setCellValue('E'.$i_row, $o_invoice->invoice_id);
                                $i_row++;
                            }
                        }
                    }
                }
                // $i_row++;
                // if ($o_student->personal_data_id == '3898a159-e5b1-4aa7-9f56-fe755db53fc6') {
                //     print('<pre>');var_dump($billing_fee);exit;
                // }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
        }
    }
}
?>