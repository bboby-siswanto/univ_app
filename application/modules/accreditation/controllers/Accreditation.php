<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Accreditation extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('Accreditation_model', 'Acm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('study_program/Study_program_model', 'Spm');
    }

    function create_filter_hash() {
        $a_custom_filter_alumni = [
            'batch' => 'all',
            'graduation_year' => ['2019', '2020', '2021', '2022'],
            'main_study_program' => 'c395b273-5acb-41c6-9d44-bccabd93d312'
        ];
        $s_filter_hash = urlencode(base64_encode(json_encode($a_custom_filter_alumni)));
        print('<pre>');var_dump($s_filter_hash);exit;
    }

    public function tracer_result($s_question_id)
    {
        $a_custom_filter_alumni = false;
        if ($this->input->is_ajax_request()) {
            $s_batch = $this->input->post('academic_year');
            $s_graduation_year = $this->input->post('graduation_year');
            $s_prodi = $this->input->post('study_program');

            if (($s_batch != 'all') OR ($s_graduation_year != 'all') OR ($s_prodi != 'all')) {
                $a_custom_filter_alumni = [
                    'batch' => $s_batch,
                    'graduation_year' => $s_graduation_year,
                    'main_study_program' => $s_prodi
                ];
            }
        }

        // $a_custom_filter_alumni = [
        //     'batch' => 'all',
        //     'graduation_year' => [2019,2020,2021,2022],
        //     'main_study_program' => 'c395b273-5acb-41c6-9d44-bccabd93d312'
        // ];

        $s_filter_hash = false;
        if ($a_custom_filter_alumni) {
            $s_filter_hash = urlencode(base64_encode(json_encode($a_custom_filter_alumni)));
        }
        // print('<pre>');var_dump($s_filter_hash);exit;
        $this->a_page_data['question_id'] = $s_question_id;
        $this->a_page_data['question_data'] = $this->General->get_where('dikti_questions', ['question_id' => $s_question_id]);
        $this->a_page_data['data_result'] = modules::run('alumni/tracer_result/get_result_for_db', $s_question_id, 'graph_data', $s_filter_hash);
        $s_html = $this->load->view('accreditation/tracer_study/tracer_result_question', $this->a_page_data, true);

        // print('<pre>');var_dump($this->a_page_data['data_result']);exit;
        if ($this->input->is_ajax_request()) {
            print json_encode(['html' => $s_html]);
        }
        else {
            // print('<pre>');var_dump($this->a_page_data['data_result']);exit;
            $this->a_page_data['body'] = $s_html;
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function tracer_dikti()
    {
        $this->a_page_data['dikti_question'] = modules::run('alumni/get_dikti_question');
        $this->a_page_data['current_year'] = $this->session->userdata('academic_year_id_active');
        $this->a_page_data['batch'] = $this->General->get_academic_year();
        $this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute(['sp.study_program_main_id' => NULL]);
        $this->a_page_data['body'] = $this->load->view('accreditation/tracer_study/list_question', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function test_status_mahasiswa() {
        $this->load->library('FeederAPI', ['mode' => 'production']);
        $array_data = [];
        $forlapperkuliahan_result = $this->feederapi->post("GetListPerkuliahanMahasiswa", [
            'filter' => "angkatan IN ('2015','2016','2017','2018','2019','2020','2021','2022') AND id_prodi IN ('208c8d88-2560-4640-a1b2-bfd42b0e7c16')"
        ]);
        if (($forlapperkuliahan_result->error_code == 0) AND (count($forlapperkuliahan_result->data) > 0)) {
            $array_data = $forlapperkuliahan_result->data;
        }
        $forlapkeluar_result = $this->feederapi->post("GetListMahasiswaLulusDO", [
            'filter' => "angkatan IN ('2015','2016','2017','2018','2019','2020','2021','2022') AND id_prodi IN ('208c8d88-2560-4640-a1b2-bfd42b0e7c16')"
        ]);
        
        if (($forlapkeluar_result->error_code == 0) AND (count($forlapkeluar_result->data) > 0)) {
            $arraykeluar_data = $forlapkeluar_result->data;
            if (count($array_data) > 0) {
                $array_data = array_merge($array_data, $arraykeluar_data);
            }
            else {
                $array_data = $arraykeluar_data;
            }
        }

        $result_data = [];
        if (count($array_data) > 0) {
            foreach ($array_data as $o_forlap_data) {
                $result_data[$o_forlap_data->id_registrasi_mahasiswa]['fullname'] = $o_forlap_data->nama_mahasiswa;
                $result_data[$o_forlap_data->id_registrasi_mahasiswa]['student_number'] = $o_forlap_data->nim;
                if (isset($o_forlap_data->id_semester)) {
                    $result_data[$o_forlap_data->id_registrasi_mahasiswa][$o_forlap_data->id_semester] = $o_forlap_data->id_status_mahasiswa;
                }
                else if (isset($o_forlap_data->id_jenis_keluar)) {
                    $s_jenis_keluar = '';
                    switch ($o_forlap_data->id_jenis_keluar) {
                        case '3':
                            $s_jenis_keluar = 'DO'; // drop out
                            break;
                        case '2':
                            $s_jenis_keluar = 'M'; // mutasi
                            break;
                        case '4':
                            $s_jenis_keluar = 'R'; // resign
                            break;
                        case '1':
                            $s_jenis_keluar = 'L'; // lulus
                            break;
                        case '6':
                            $s_jenis_keluar = 'W'; // 
                            break;
                        default:
                            break;
                    }
                    $result_data[$o_forlap_data->id_registrasi_mahasiswa][$o_forlap_data->id_periode_keluar] = $s_jenis_keluar;
                }
            }
            $result_data = array_values($result_data);
        }
        print('<pre>');var_dump($result_data);exit;
    }

    function cek_datakeluar() {
        $this->load->library('FeederAPI', ['mode' => 'production']);
        $forlapkeluar_result = $this->feederapi->post("GetListMahasiswaLulusDO", [
            // 'filter' => "angkatan IN ('".implode("','", $a_student_batch)."') AND id_prodi IN ('".implode("','", $a_student_prodi)."') AND id_status_mahasiswa IN ('A','N')"
            'filter' => "tanggal_keluar IS NULL"
        ]);
        
        print('<pre>');var_dump($forlapkeluar_result);exit;
    }

    public function get_student_status()
    {
        if ($this->input->is_ajax_request()) {
            // $a_student_batch = ['2016', '2017'];
            // $a_student_prodi = ['6266e096-63ad-4b77-82b0-17216155a70e'];
            $a_student_batch = $this->input->post('filter_batch[]');
            $a_student_prodi = $this->input->post('filter_prodi[]');
            $a_list_status = [
                'aktif' => 0,
                'merdeka' => 0,
                'non aktif' => 0,
                'cuti' => 0,
                'resign' => 0,
                'drop out' => 0,
                'lulus' => 0
            ];
            $a_status_result = [];
            // $a_student_status = $this->input->post('filter_status[]');

            // $a_batch = array_values($a_student_batch);
            $mba_student_data = false;
            $start_year = ((is_array($a_student_batch)) AND count($a_student_batch) > 0) ? $a_student_batch[count($a_student_batch) - 1] : false;
            $end_year = $this->session->userdata('academic_year_id_active');

            $forlap_result = [];
            if ((!empty($a_student_batch)) AND (!empty($a_student_prodi))) {
                $this->load->library('FeederAPI', ['mode' => 'production']);
                for ($i=$start_year; $i <= $end_year ; $i++) {
                    $a_status_result[$i.'1']['aktif'] = 0;
                    $a_status_result[$i.'2']['aktif'] = 0;
                    $a_status_result[$i.'3']['aktif'] = 0;
                    $a_status_result[$i.'1']['merdeka'] = 0;
                    $a_status_result[$i.'2']['merdeka'] = 0;
                    $a_status_result[$i.'3']['merdeka'] = 0;
                    $a_status_result[$i.'1']['non aktif'] = 0;
                    $a_status_result[$i.'2']['non aktif'] = 0;
                    $a_status_result[$i.'3']['non aktif'] = 0;
                    $a_status_result[$i.'1']['cuti'] = 0;
                    $a_status_result[$i.'2']['cuti'] = 0;
                    $a_status_result[$i.'3']['cuti'] = 0;
                    $a_status_result[$i.'1']['resign'] = 0;
                    $a_status_result[$i.'2']['resign'] = 0;
                    $a_status_result[$i.'3']['resign'] = 0;
                    $a_status_result[$i.'1']['drop out'] = 0;
                    $a_status_result[$i.'2']['drop out'] = 0;
                    $a_status_result[$i.'3']['drop out'] = 0;
                    $a_status_result[$i.'1']['lulus'] = 0;
                    $a_status_result[$i.'2']['lulus'] = 0;
                    $a_status_result[$i.'3']['lulus'] = 0;
                }

                $forlapkuliah_result = $this->feederapi->post("GetListPerkuliahanMahasiswa", [
                    // 'filter' => "angkatan IN ('".implode("','", $a_student_batch)."') AND id_prodi IN ('".implode("','", $a_student_prodi)."') AND id_status_mahasiswa IN ('A','N')"
                    'filter' => "angkatan IN ('".implode("','", $a_student_batch)."') AND id_prodi IN ('".implode("','", $a_student_prodi)."')"
                ]);
                $forlapkeluar_result = $this->feederapi->post("GetListMahasiswaLulusDO", [
                    // 'filter' => "angkatan IN ('".implode("','", $a_student_batch)."') AND id_prodi IN ('".implode("','", $a_student_prodi)."') AND id_status_mahasiswa IN ('A','N')"
                    'filter' => "angkatan IN ('".implode("','", $a_student_batch)."') AND id_prodi IN ('".implode("','", $a_student_prodi)."')"
                ]);

                if (($forlapkuliah_result->error_code == 0) AND (count($forlapkuliah_result->data) > 0)) {
                    $forlap_result = $forlapkuliah_result->data;
                }

                if (($forlapkeluar_result->error_code == 0) AND (count($forlapkeluar_result->data) > 0)) {
                    $arraykeluar_data = $forlapkeluar_result->data;
                    if (count($forlap_result) > 0) {
                        $forlap_result = array_merge($forlap_result, $arraykeluar_data);
                    }
                    else {
                        $forlap_result = $arraykeluar_data;
                    }
                }

                // $a_list_status = ['aktif','non aktif','cuti','resign','drop out','lulus'];
                if (count($forlap_result) > 0) {
                    $a_forlap_result_data = [];
                    foreach ($forlap_result as $o_forlap_data) {
                        $mba_forlap_biodata = $this->feederapi->post("GetListRiwayatPendidikanMahasiswa", [
                            'filter' => "id_registrasi_mahasiswa='$o_forlap_data->id_registrasi_mahasiswa'"
                        ]);
                        $mbo_forlap_biodata = (($mba_forlap_biodata->error_code == 0) AND (count($mba_forlap_biodata->data) > 0)) ? $mba_forlap_biodata->data[0] : false;
                        // print('<pre>');var_dump($mba_forlap_biodata);exit;
                        $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa]['fullname'] = $o_forlap_data->nama_mahasiswa;
                        $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa]['student_number'] = $o_forlap_data->nim;
                        $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa]['prodi'] = $o_forlap_data->nama_program_studi;
                        $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa]['jenis_daftar'] = ($mbo_forlap_biodata) ? $mbo_forlap_biodata->nama_jenis_daftar : '';
                        $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa]['periode_masuk'] = ($mbo_forlap_biodata) ? $mbo_forlap_biodata->id_periode_masuk : '';

                        if (isset($o_forlap_data->id_jenis_keluar)) {
                            $s_jenis_keluar = '';
                            if ((!isset($o_forlap_data->id_periode_keluar)) OR (is_null($o_forlap_data->id_periode_keluar))) {
                                $s_yearforlap = date('Y', strtotime($o_forlap_data->tanggal_keluar));
                                $s_monthforlap = date('m', strtotime($o_forlap_data->tanggal_keluar));
                                if (is_null($o_forlap_data->tanggal_keluar)) {
                                    $s_yearforlap = $o_forlap_data->angkatan;
                                    $s_monthforlap = date('m', strtotime($o_forlap_data->tgl_masuk_sp));
                                }
                                $s_monthforlap = (intval($s_monthforlap) >= 7) ? 1 : 2;
                                $o_forlap_data->id_periode_keluar = $s_yearforlap.$s_monthforlap;
                                

                                // $this->db->where('semester_end_date >= ', date('Y-m-d', strtotime($o_forlap_data->tanggal_keluar)).' 00:00:00');
                                // $this->db->where('semester_start_date <= ', date('Y-m-d', strtotime($o_forlap_data->tanggal_keluar)).' 00:00:00');
                                // $query_get = $this->db->get('dt_semester_settings');
                                // if ($query_get->num_rows() > 0) {
                                //     $data = $query_get->first_row();
                                //     $o_forlap_data->id_periode_keluar = $data->academic_year_id.$data->semester_type_id;
                                //     print('<pre>');var_dump($o_forlap_data->id_periode_keluar);exit;
                                // }

                                // print('<pre>');var_dump($this->db->last_query());exit;
                            }

                            if (!isset($o_forlap_data->id_periode_keluar)) {
                                print('<pre>');var_dump($o_forlap_data);exit;
                            }
                            switch ($o_forlap_data->id_jenis_keluar) {
                                case '3':
                                    $a_status_result[$o_forlap_data->id_periode_keluar]['drop out']++;
                                    $s_jenis_keluar = 'DO';
                                    break;
                                case '2':
                                    $s_jenis_keluar = 'M'; // mutasi
                                    break;
                                case '4':
                                    $a_status_result[$o_forlap_data->id_periode_keluar]['resign']++;
                                    $s_jenis_keluar = 'R';
                                    break;
                                case '1':
                                    $a_status_result[$o_forlap_data->id_periode_keluar]['lulus']++;
                                    $s_jenis_keluar = 'L';
                                    break;
                                case '6':
                                    $s_jenis_keluar = 'W'; // 
                                    break;
                                default:
                                    break;
                            }
                            $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa][$o_forlap_data->id_periode_keluar] = $s_jenis_keluar;
                        }
                        else if (isset($o_forlap_data->id_semester)) {
                            if ($o_forlap_data->id_status_mahasiswa == 'A') {
                                $a_status_result[$o_forlap_data->id_semester]['aktif']++;
                            }
                            else if ($o_forlap_data->id_status_mahasiswa == 'M') {
                                $a_status_result[$o_forlap_data->id_semester]['merdeka']++;
                            }
                            else if ($o_forlap_data->id_status_mahasiswa == 'N') {
                                $a_status_result[$o_forlap_data->id_semester]['non aktif']++;
                            }
                            else if ($o_forlap_data->id_status_mahasiswa == 'C') {
                                $a_status_result[$o_forlap_data->id_semester]['cuti']++;
                            }
                            $a_forlap_result_data[$o_forlap_data->id_registrasi_mahasiswa][$o_forlap_data->id_semester] = $o_forlap_data->id_status_mahasiswa;
                        }
                    }
                    $a_forlap_result_data = array_values($a_forlap_result_data);
                    // print('<pre>');var_dump($a_forlap_result_data);exit;
                    $mba_student_data = $a_forlap_result_data;
                }
            }
            
            $this->a_page_data['list_data'] = $mba_student_data;
            $this->a_page_data['start_year_header'] = $start_year;
            $this->a_page_data['end_year_header'] = $end_year;
            $this->a_page_data['list_status'] = $a_status_result;
            $view_data = $this->load->view('table/status_list_table', $this->a_page_data, true);
            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     $view_data = $this->load->view('table/status_result_table', $this->a_page_data, true);
            // }
            // $view_data = '<h1>Sedang maintenance</h1>';
            // print($view_data);exit;
            print json_encode(['data' => $view_data, 'forlap_data' => $mba_student_data]);
            // exit;
        }
    }

    public function student_status()
    {
        $this->a_page_data['current_year'] = $this->session->userdata('academic_year_id_active');
        $this->a_page_data['batch'] = $this->General->get_academic_year();
        $this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute(['sp.study_program_main_id' => NULL]);
        $this->a_page_data['body'] = $this->load->view('accreditation/status_student', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function lecturer_teaching()
    {
        $this->a_page_data['batch'] = $this->General->get_academic_year();
        $this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute(['sp.study_program_main_id' => NULL]);
        $this->a_page_data['body'] = $this->load->view('accreditation/lecturer_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_list_lecturer_class()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year');
            $s_study_program_id = $this->input->post('study_program');

            $mba_data = $this->get_lecturer_list_data($s_academic_year_id, $s_study_program_id);

            print json_encode(['data' => $mba_data]);
        }
    }

    public function list_subject_skipped()
    {
        $a_subject_sum_skipped = ['research semester','project research','research project','internship','thesis','nfu'];
        return $a_subject_sum_skipped;
    }

    public function test_get_list_lecturer($s_academic_year_id, $s_study_program_id) {
        $mba_data = $this->get_lecturer_list_data($s_academic_year_id, $s_study_program_id);
        print('<pre>');var_dump($mba_data);exit;
    }

    public function get_lecturer_list_data($s_academic_year_id, $s_study_program_id)
    {
        $a_subject_sum_skipped = $this->list_subject_skipped();
        $a_class_filter = [
            'cm.academic_year_id' => $s_academic_year_id,
            'ofs.study_program_id' => $s_study_program_id
        ];

        if ($s_study_program_id == 'all') {
            unset($a_class_filter['ofs.study_program_id']);
        }
        
        $a_prodi_selected = [$s_study_program_id];
        $mba_class_master_list = $this->Acm->get_class_master($a_class_filter, [1,2]);
        $mba_is_prodi_main = $this->General->get_where('ref_study_program', ['study_program_main_id' => $s_study_program_id]);
        if ($mba_is_prodi_main) {
            $a_prodi_child = [$s_study_program_id];
            foreach ($mba_is_prodi_main as $o_prodi) {
                if (!in_array($o_prodi->study_program_id, $a_prodi_child)) {
                    array_push($a_prodi_child, $o_prodi->study_program_id);
                }
            }

            unset($a_class_filter['ofs.study_program_id']);
            $mba_class_master_list = $this->Acm->get_class_master($a_class_filter, [1,2], $a_prodi_child);
            // print('<pre>');var_dump($a_prodi_child);exit;
            $a_prodi_selected = $a_prodi_child;
        }

        // print('<pre>');var_dump($mba_class_master_list);exit;
        $mba_data = false;
        $a_lecturer_reported = [];
        if ($mba_class_master_list) {
            foreach ($mba_class_master_list as $o_class_master) {
                $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer([
                    'class_master_id' => $o_class_master->class_master_id
                ]);
                
                if ($mba_class_master_lecturer) {
                    foreach ($mba_class_master_lecturer as $o_lecturer) {
                        if (!in_array($o_lecturer->employee_id_reported, $a_lecturer_reported)) {
                            if (!is_null($o_lecturer->employee_lecturer_number)) {
                                array_push($a_lecturer_reported, $o_lecturer->employee_id_reported);
                            }
                        }
                    }
                }
            }
        }

        if (count($a_lecturer_reported) > 0) {
            $mba_data = [];
            foreach ($a_lecturer_reported as $s_employee_id) {
                $mba_data_lecturer = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);
                if ($mba_data_lecturer) {
                    $o_employee_data = $mba_data_lecturer[0];
                    $d_total_sks_prodi = 0;
                    $d_total_sks_ot_prodi = 0;

                    $mba_class_lecturer = $this->Acm->get_class_master([
                        'cm.academic_year_id' => $s_academic_year_id,
                        'cml.employee_id_reported' => $s_employee_id
                    ], [1,2]);

                    if ($mba_class_lecturer) {
                        foreach ($mba_class_lecturer as $o_class_lecturer) {
                            $mba_personal_data_employee = $this->Emm->get_employee_data(['em.employee_id' => $o_class_lecturer->employee_id]);
                            $mba_class_prodi = $this->Cgm->get_class_master_study_program($o_class_lecturer->class_master_id);
                            $mba_class_student = $this->Cgm->get_class_master_student($o_class_lecturer->class_master_id);
                            $mba_class_absence = $this->General->get_where('dt_class_subject_delivered', [
                                'class_master_id' => $o_class_lecturer->class_master_id,
                                'employee_id' => $o_class_lecturer->employee_id
                            ]);

                            $b_allow_calculate = ($mba_class_student) ? true : false;
                            foreach ($a_subject_sum_skipped as $s_subject) {
                                if (strpos(strtolower($o_class_lecturer->subject_name), $s_subject) !== false) {
                                    $b_allow_calculate = false;
                                }
                            }

                            $a_prodi = [];
                            $a_prodi_id = [];

                            if ($mba_class_prodi) {
                                foreach ($mba_class_prodi as $o_prodi) {
                                    if (in_array($o_prodi->study_program_id, $a_prodi_selected)) {
                                        $mba_student_class = $this->Scm->get_student_krs_class([
                                            'sc.class_master_id' => $o_class_lecturer->class_master_id,
                                            'sc.score_approval' => 'approved'
                                        ], $a_prodi_selected, 'st.study_program_id');

                                        if (($b_allow_calculate) AND ($mba_student_class)) {
                                            $d_total_sks_prodi += $o_class_lecturer->credit_allocation;
                                        }
                                    }
                                    else {
                                        $mba_student_class = $this->Scm->get_score_data([
                                            'sc.class_master_id' => $o_class_lecturer->class_master_id,
                                            'st.study_program_id' => $o_prodi->study_program_id,
                                            'sc.score_approval' => 'approved'
                                        ]);

                                        if (($b_allow_calculate) AND ($mba_student_class)) {
                                            $d_total_sks_ot_prodi += $o_class_lecturer->credit_allocation;
                                        }
                                    }

                                    // if ($s_employee_id == '6aa4f131-b15a-4f05-95c0-c3cb7a476900') {
                                    //     print('<pre>');var_dump($mba_class_lecturer);exit;
                                    // }

                                    // if (($b_allow_calculate) AND ($mba_student_class)) {
                                    //     if ($s_study_program_id == $o_prodi->study_program_id) {
                                    //         $d_total_sks_prodi += $o_class_lecturer->credit_allocation;
                                    //     }
                                    //     else {
                                    //         $d_total_sks_ot_prodi += $o_class_lecturer->credit_allocation;
                                    //     }
                                    // }
                                    
                                    $s_prodi_abbr = $o_prodi->study_program_abbreviation;
                                    if (!is_null($o_prodi->study_program_main_id)) {
                                        $mba_prodi_main = $this->General->get_where('ref_study_program', ['study_program_id' => $o_prodi->study_program_main_id]);
                                        $s_prodi_abbr = $mba_prodi_main[0]->study_program_abbreviation.'('.$o_prodi->study_program_abbreviation.')';
                                    }

                                    if (!in_array($s_prodi_abbr, $a_prodi)) {
                                        array_push($a_prodi, $s_prodi_abbr);
                                        array_push($a_prodi_id, $o_prodi->study_program_id);
                                    }
                                }
                            }
                            
                            $o_class_lecturer->this_calculated = $b_allow_calculate;
                            $o_class_lecturer->class_employee_name = $mba_personal_data_employee[0]->personal_data_name;
                            $o_class_lecturer->class_prodi = implode(' / ', $a_prodi);
                            $o_class_lecturer->class_prodi_data = $mba_class_prodi;
                            $o_class_lecturer->class_prodi_id = implode('/', $a_prodi_id);
                            $o_class_lecturer->class_student = ($mba_class_student) ? count($mba_class_student) : 0;
                            $o_class_lecturer->class_lecturer_absence = ($mba_class_absence) ? count($mba_class_absence) : 0;
                        }
                    }

                    $o_employee_data->lecturer_fullname = $this->General->retrieve_title($o_employee_data->personal_data_id);
                    $o_employee_data->total_sks_prodi = number_format($d_total_sks_prodi,2);
                    $o_employee_data->total_sks_ot_prodi = number_format($d_total_sks_ot_prodi,2);
                    $o_employee_data->class_data = $mba_class_lecturer;
                    $o_employee_data->prodi_selected = $a_prodi_selected;
                    array_push($mba_data, $o_employee_data);
                }
            }
        }

        return $mba_data;
    }
}
