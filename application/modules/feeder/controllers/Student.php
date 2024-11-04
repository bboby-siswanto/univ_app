<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

class Student extends App_core
{
    private $id_pt = '7ed83401-2862-4e12-aa60-79defe7b90a8';
    private $s_stage = '';
    private $i_total_data = 0;
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('personal_data/Family_model', 'Fmm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Subject_model', 'Sbm');
        $this->load->model('finance/Invoice_model', 'Inv');
        $this->load->model('academic/Subject_model', 'Sbj');
        $this->load->model('admission/International_office_model', 'Iom');
        $this->load->model('feeder/Feeder_model', 'Fem');

        // $this->load->library('FeederAPI');
        $this->load->library('FeederAPI', ['mode' => 'production']);
    }

    public function feeder_gpa($s_prodi_dikti, $s_batch, $s_semester_dikti)
    {
        $a_prodi_id = [$s_prodi_dikti];
        $prodi_data = $this->General->get_where('ref_study_program', ['study_program_main_id' => $s_prodi_dikti]);
        if ($prodi_data) {
            foreach ($prodi_data as $o_prodi) {
                array_push($a_prodi_id, $o_prodi->study_program_id);
            }
        }
        $a_status = ['active', 'inactive', 'graduated', 'resign', 'onleave'];
        $mba_student_data = $this->Stm->get_student_filtered(false, $a_status, 'ds.student_number', $a_prodi_id);
        // print('<pre>');var_dump($mba_student_data);exit;
        print('<table border="1">');
        print('<tr>');
        print('<td rowspan="2">Nama Mahasiswa</td><td rowspan="2">NIM</td>');   
        print('<td colspan="2">Status Semester</td><td colspan="2">Status</td>');
        print('<td colspan="2">SKS Semester</td><td colspan="2">SKS Total</td>');
        print('<td colspan="2">IPS</td><td colspan="2">IPK</td>');
        print('</tr>');
        print('<tr>');
        print('<td>Portal</td><td>Forlap</td>');
        print('<td>Portal</td><td>Forlap</td>');
        print('<td>Portal</td><td>Forlap</td>');
        print('<td>Portal</td><td>Forlap</td>');
        print('<td>Portal</td><td>Forlap</td>');
        print('<td>Portal</td><td>Forlap</td>');
        print('</tr>');

        if ($mba_student_data) {
            foreach ($mba_student_data as $o_student) {
                $_feeder_semester = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
                    'filter' => "id_registrasi_mahasiswa='$o_student->student_id'"
                ));

                print('<pre>');var_dump($_feeder_semester);exit;

                print('<tr>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td>'.$o_student->student_number.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.$o_student->student_status.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('<td>'.''.'</td>');
                print('</tr>');
            }
        }
        print('</table>');
    }

    public function sync_semester()
    {
        print('mohon maaf, warung tutup!');exit;
        // if ($this->input->is_ajax_request()) {
        //     $s_academic_year_id = $this->input->post('academic_year_id');
            // $s_semester_type_id = $this->input->post('semester_type_id');
            $s_academic_year_id = 2020;
            $s_semester_type_id = 1;
            
            $_feeder_semester = $this->feederapi->post('GetSemester', array(
                'filter' => "id_tahun_ajaran = '$s_academic_year_id' AND semester = '$s_semester_type_id'"
            ));

            // print('<pre>');
            // var_dump($_feeder_semester);
            // var_dump($this->input->post());exit;

            if ($_feeder_semester->error_code == 0) {
                $s_id_semester = $_feeder_semester->data[0]->id_semester;

                $this->student_sync($s_academic_year_id, $s_semester_type_id);
                $this->student_semester_sync($s_academic_year_id, $s_semester_type_id, $s_id_semester);

                $a_return = array('code' => 0, 'message' => 'Success!');
            }else{
                $a_return = array('code' => 1, 'message' => 'Semester not found in feeder!');
                $this->print_result('error', 'Error sinkronisasi: Semester tidak ditemukan di feeder!');
                exit;
            }

            print('<h3 class="text-primary">Finish</h3>');

            // print('<pre>');var_dump($a_return);
            print json_encode($a_return);
            // print($a_return['message'].'<br>');
        // }
    }

    function test_acti() {
        $a_score_forlap = $this->feederapi->post('GetListPerkuliahanMahasiswa', [
            'filter' => "id_registrasi_mahasiswa='ea881d6d-21be-4858-bdfd-3dd30ed44eda' AND id_semester='20231'"
        ]);
        print('<pre>');var_dump($a_score_forlap);exit;
    }

    public function _get_biaya_masuk($o_student, $s_id_mahasiswa)
    {
        $mbo_fee_data = $this->Inv->student_has_invoice_data($s_id_mahasiswa, [
            'df.study_program_id' => $o_student->study_program_id,
            'df.payment_type_code' => '02',
            'df.academic_year_id' => $o_student->finance_year_id,
            'df.fee_amount_type' => 'main',
            'df.program_id' => $o_student->program_id
        ]);

        $d_biaya = 0;
        $s_id_pembiayaan = '1';
        if ($mbo_fee_data) {
            $d_harga_utama = $mbo_fee_data->invoice_details_amount;
            $s_invoice_id = $mbo_fee_data->invoice_id;
            $mba_invoice_details = $this->Inv->get_invoice_details(['did.invoice_id' => $s_invoice_id]);
            foreach ($mba_invoice_details as $o_invoice_details) {
                if ($o_invoice_details->invoice_details_amount_sign_type == 'positive') {
                    if ($o_invoice_details->invoice_details_amount_number_type == 'number') {
                        $d_biaya += $o_invoice_details->invoice_details_amount;
                    }else{
                        $d_harga = $d_harga_utama * $o_invoice_details->invoice_details_amount / 100;
                        $d_biaya += $d_harga;
                    }
                }else{
                    if ($o_invoice_details->invoice_details_amount_number_type == 'number') {
                        $d_biaya -= $o_invoice_details->invoice_details_amount;
                    }else{
                        $d_harga = $d_harga_utama * $o_invoice_details->invoice_details_amount / 100;
                        $d_biaya -= $d_harga;
                    }
                }
            }
        }
        $d_biaya = ($o_student->student_status != 'active') ? 0 : $d_biaya;

        return $d_biaya;
    }

    public function get_biaya_semester($s_batch, $s_faculty_id, $mbi_credit_semester = false)
    {
        $i_biaya = 0;

        # faculty_id:
        #Engineering = 301a3e19-348d-4398-b640-c9d2acc491fa
        #Life Sciences = 51e2f6ff-c394-44c1-8658-7bd9dd46c654
        #Business and Social Sciences = f9f52242-983d-4e4c-8d5c-7c4575de0679

        switch ($s_batch) {
            case '2019':
                if ($s_faculty_id == '301a3e19-348d-4398-b640-c9d2acc491fa') {
                    $i_biaya = 26400000;
                }else if ($s_faculty_id == '51e2f6ff-c394-44c1-8658-7bd9dd46c654') {
                    $i_biaya = 26400000;
                }else if ($s_faculty_id == 'f9f52242-983d-4e4c-8d5c-7c4575de0679') {
                    $i_biaya = 25200000;
                }

                break;

            case '2018':
                if ($s_faculty_id == '301a3e19-348d-4398-b640-c9d2acc491fa') {
                    $i_biaya = 28800000;
                }else if ($s_faculty_id == '51e2f6ff-c394-44c1-8658-7bd9dd46c654') {
                    $i_biaya = 28800000;
                }else if ($s_faculty_id == 'f9f52242-983d-4e4c-8d5c-7c4575de0679') {
                    $i_biaya = 27000000;
                }

                break;
            
            case '2017':
                if ($s_faculty_id == '301a3e19-348d-4398-b640-c9d2acc491fa') {
                    $i_biaya = 32000000;
                }else if ($s_faculty_id == '51e2f6ff-c394-44c1-8658-7bd9dd46c654') {
                    $i_biaya = 32000000;
                }else if ($s_faculty_id == 'f9f52242-983d-4e4c-8d5c-7c4575de0679') {
                    $i_biaya = 29000000;
                }

                break;

            case '2016':
                if ($s_faculty_id == '301a3e19-348d-4398-b640-c9d2acc491fa') {
                    $i_biaya = 34000000;
                }else if ($s_faculty_id == '51e2f6ff-c394-44c1-8658-7bd9dd46c654') {
                    $i_biaya = 34000000;
                }else if ($s_faculty_id == 'f9f52242-983d-4e4c-8d5c-7c4575de0679') {
                    $i_biaya = 31000000;
                }

                break;

            case '2015':
                // mampus per SKS
                # 1 sks = 1350000;
                $i_biaya = intval($mbi_credit_semester) * 1350000;
                break;

            default:
                break;
        }

        return $i_biaya;
    }

    public function print_result($s_type, $s_message = '', $i_current_process = 0)
    {
        // $result = ($i_current_process / $i_total_data) * 100;
        // $_percentage = round($result, 2, PHP_ROUND_HALF_UP);

        $a_print = array(
            'stage' => $this->s_stage,
            'total_data' => $this->i_total_data,
            'current_process' => $i_current_process
        );

        $s_print_message = '';
        if ($s_type == 'error') {
            $a_print['message'] = '<span class="text-danger"><i class="fas fa-dot-circle"></i> '.$s_message.'</span><br>';
            // print('<span class="text-danger">'.$s_message.'</span><br>');
        }else if($s_type == 'success'){
            $a_print['message'] = '<span class="text-primary"><i class="fas fa-dot-circle"></i> '.$s_message.'</span><br>';
            // print('<span class="text-primary">'.$s_message.'</span><br>');
        }else{
            $a_print['message'] = '<span>'.$s_message.'</span><br>';
            // print('<span>'.$s_message.'</span><br>');
        }

        print json_encode($a_print);
    }

    public function get_registration_id($s_id_mahasiswa)
    {
        $a_feeder_data = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
            // 'filter' => "nama_mahasiswa = '$student->personal_data_name' AND tempat_lahir = '$student->personal_data_place_of_birth' AND tanggal_lahir = '$student->personal_data_date_of_birth' AND nama_ibu_kandung = '$student->personal_data_mother_maiden_name'"
            'filter' => "id_mahasiswa = '$s_id_mahasiswa'"
        ));

        $s_id_registrasi_mahasiswa = false;

        if ($a_feeder_data->error_code != '0') {
            $a_result = array('code' => 99999, 'message' => 'Error sinkronisasi: '.$a_feeder_data->error_desc, 'process' => $a_feeder_data);
            print('<pre>');var_dump($a_result);exit;
        }else if (count($a_feeder_data->data) > 0) {
            $data = $a_feeder_data->data[0];
            $s_id_registrasi_mahasiswa = $data->id_registrasi_mahasiswa;
        }

        return $s_id_registrasi_mahasiswa;
        // var_dump($s_id_registrasi_mahasiswa);
    }

    public function sync_student_semester_all()
    {
        $mba_study_program = $this->General->get_where('ref_study_program');
        if ($mba_study_program) {
            foreach ($mba_study_program as $o_prodi) {
                if ($o_prodi->study_program_id != '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8') {
                    $this->sync_student_semester('all', $o_prodi->study_program_id, '20211');
                }
            }
        }
    }

    public function sync_from_excel()
    {
        print('closed!');exit;
        $s_template_path = APPPATH.'uploads/temp/for_sync.xlsx';
		$o_spreadsheet = IOFactory::load("$s_template_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $i_row = 2;
        while ($o_sheet->getCell('A'.$i_row)->getValue() !== NULL) {
            $s_student_name = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('A'.$i_row)->getValue()));
            $s_tanggal_sk_yudisium = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('E'.$i_row)->getValue()));
            $s_sk_yudisium = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('F'.$i_row)->getValue()));
            $s_pin_number = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('G'.$i_row)->getValue()));
            $s_ipk = str_replace('"', '', str_replace('=', '', $o_sheet->getCell('H'.$i_row)->getValue()));
            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_name' => $s_student_name,
                'ds.student_status' => 'graduated'
            ]);
            $o_student = $mba_student_data[0];
            $s_id_reg_mahasiswa = $o_student->student_id;
            
            $a_feeder_data = array(
                'id_jenis_keluar' => '1',
                'tanggal_keluar' => $s_tanggal_sk_yudisium,
                // 'keterangan' => '',
                'nomor_sk_yudisium' => $s_sk_yudisium,
                'tanggal_sk_yudisium' => $s_tanggal_sk_yudisium,
                'ipk' => $s_ipk,
                'nomor_ijazah' => $o_student->student_pin_number,
                // 'jalur_skripsi' => '',
                'judul_skripsi' => $o_student->student_thesis_title,
                // 'bulan_awal_bimbingan' => '',
                // 'bulan_akhir_bimbingan' => '',
                'id_periode_keluar' => '20212'
            );

            $a_feeder_list_perkuliahan_data = $this->feederapi->post('GetDetailMahasiswaLulusDO', array(
                'filter' => "id_registrasi_mahasiswa = '$s_id_reg_mahasiswa'"
            ));
            // print('<pre>');var_dump($a_feeder_list_perkuliahan_data);exit;

            if ($a_feeder_list_perkuliahan_data->error_code != '0') {
                print('Error get data from feeder: ');
                var_dump($a_feeder_list_perkuliahan_data);exit;
            }else if (count($a_feeder_list_perkuliahan_data->data) == 0) {
                print('insert ');
                $a_feeder_data['id_registrasi_mahasiswa'] = $s_id_reg_mahasiswa;
                $a_result = $this->feederapi->post('InsertMahasiswaLulusDO', array(
                    'record' => $a_feeder_data
                ));
            }else{
                print('update ');
                $a_result = $this->feederapi->post('UpdateMahasiswaLulusDO', array(
                    'key' => array(
                        'id_registrasi_mahasiswa' => $s_id_reg_mahasiswa
                    ),
                    'record' => $a_feeder_data
                ));
            }

            if ($a_result->error_code == 0) {
                print('success '.$s_student_name);
            }else{
                print('Error submit to feeder ');
                var_dump($a_result);exit;
            }
            print('<br>');

            $i_row++;
        }

        print('<h1>Finish</h1>');exit;
    }

    public function sync_student_semester_custom()
    {
        $this->sync_student_semester('2021', '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8', '20211', true);
    }

    public function test_student_semester()
    {
        $mba_student_semester = $this->Smm->get_student_semester([
            'dss.academic_year_id' => 2022,
            'dss.semester_type_id' => 1
        ]);
        if ($mba_student_semester) {
            foreach ($mba_student_semester as $o_semester) {
                if ($o_semester->student_semester_status == 'resign') {
                    # code...
                }
            }
        }
    }

    public function sync_student_semester($s_batch, $s_study_program_id, $s_semester_dikti = '20232', $b_sync_keluar = false)
    {
        $a_perkuliahan_gagal = [];
        $s_academic_year_id = substr($s_semester_dikti, 0, 4);
        $s_semester_type_id = substr($s_semester_dikti, 4, 1);

        $a_filter = array(
            'dss.academic_year_id' => $s_academic_year_id,
            'dss.semester_type_id' => $s_semester_type_id,
            // 'st.student_status' => 'active'
        );

        if (($s_batch != 'all') AND (!empty($s_batch))) {
            $a_filter['st.academic_year_id'] = $s_batch;
        }

        if (($s_study_program_id != 'all') AND (!empty($s_study_program_id))) {
            $a_filter['st.study_program_id'] = $s_study_program_id;
            $o_prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id]);
            $s_prodi = $o_prodi_data[0]->study_program_name;
        }
        else {
            $s_prodi = 'all';
        }

        // print('===================================Sinkronisasi perkuliahan mahasiswa===================================<br>');
        $this->print_result('', '===================================Sinkronisasi perkuliahan mahasiswa===================================');
        $this->print_result('', '===================================Prodi '.$s_prodi.'===================================');
        $mba_student_semester = $this->Smm->get_student_semester($a_filter);
        $s_dikti_semester = (in_array($s_semester_type_id, [7,8])) ? $s_academic_year_id.'3' : $s_semester_dikti;

        if ($mba_student_semester) {
            foreach ($mba_student_semester as $student_semester) {
                if (is_null($student_semester->student_not_reported_to_feeder)) {
                    $s_id_mahasiswa = $student_semester->personal_data_id;
                    $s_id_registrasi = $student_semester->student_id;
                    
                    $a_feeder_data = array(
                        'id_mahasiswa' => $s_id_mahasiswa,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'id_semester' => $s_dikti_semester,
                        'id_registrasi_mahasiswa' => $s_id_registrasi,
                        'student_semester_data' => $student_semester,
                        'id_pembiayaan' => '1'
                    );

                    $mba_feeder_student_data = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', [
                        'filter' => "id_registrasi_mahasiswa = '$s_id_registrasi'"
                    ]);

                    if (count($mba_feeder_student_data->data) > 0) {
                        if (in_array($student_semester->student_semester_status, array('active', 'inactive', 'onleave','graduated'))) {
                            $post_insert_perkuliahan = $this->post_insert_perkuliahan_mahasiswa($a_feeder_data);
                            if ($post_insert_perkuliahan['code'] == 0) {
                                $this->print_result('success', 'Sinkronisasi perkuliahan mahasiswa '.$student_semester->personal_data_name.' berhasil:');
                            }else{
                                $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $student_semester->student_id))[0];
                                array_push($a_perkuliahan_gagal, array(
                                    'student_id' => $student_semester->student_id,
                                    'student_number' => $mba_student_data->student_number,
                                    'study_program' => $mba_student_data->study_program_name,
                                    'personal_data_name' => $student_semester->personal_data_name,
                                    'error_desc' => $post_insert_perkuliahan['message']
                                ));
                                // print('<pre>032'.$student_semester->personal_data_name.'<br>');var_dump($post_insert_perkuliahan);exit;
                                $this->print_result('error', 'Sinkronisasi perkuliahan mahasiswa '.$student_semester->personal_data_name.' gagal: error ('.$s_id_mahasiswa.') - '.$post_insert_perkuliahan['message']);
                            }
                        }
                        // else 
                        // if (in_array($student_semester->student_semester_status, ['resign', 'graduated', 'dropout'])) {
                        //     if ($b_sync_keluar) {
                        //         $post_sync_mahasiswa_lulus_do = $this->post_mahasiswa_lulus_do($a_feeder_data);
                        //         if ($post_sync_mahasiswa_lulus_do['code'] == 0) {
                        //             $this->print_result('success', 'Sinkronisasi mahasiswa keluar '.$student_semester->personal_data_name.' berhasil:');
                        //         }else{
                        //             $this->print_result('error', 'Sinkronisasi mahasiswa keluar '.$student_semester->personal_data_name.' gagal: error ('.$s_id_mahasiswa.') - '.$post_sync_mahasiswa_lulus_do['message']);
                        //         }
                        //         exit;
                        //     }
                        // }
                    }
                }
            }
        }else{
            $this->print_result('error', 'tidak ada mahasiswas di semester terpilih '.$s_semester_dikti.': prodi ('.$s_study_program_id.')-'.json_encode($a_filter));
        }
    }

    public function set_id_transfer_score()
    {
        // print('<pre>');var_dump($this->db);exit;
        $a_dikti_data_transfer = $this->feederapi->post('GetNilaiTransferPendidikanMahasiswa');
        $i = 0;
        $a_student_name = array();
        if ($a_dikti_data_transfer->error_code == '0') {
            $a_data = $a_dikti_data_transfer->data;
            print('<pre>');
            var_dump($a_data);exit;
            foreach ($a_data as $feeder_data) {
                $s_student_id = $feeder_data->id_registrasi_mahasiswa;
                $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
                if ($mbo_student_data) {
                    # check score
                    $mba_score_data = $this->Scm->get_score_data(array(
                        'sc.student_id' => $s_student_id,
                        'sc.semester_type_id' => 5,
                        'sn.subject_name' => $feeder_data->nama_mata_kuliah_diakui,
                        'sc.score_approval' => 'approved'
                    ));

                    if ($mba_score_data) {
                        if (count($mba_score_data) > 1) {
                            // var_dump($mba_score_data);
                            print('Duplicate subject for student '.$mba_score_data[0]->personal_data_name.'id:'.$mba_score_data->student_id.', curriculum_subject_id: '.$mba_score_data[0]->curriculum_subject_id);
                        }else{
                            $saved = $this->Scm->save_data(array('score_id' => $feeder_data->id_transfer), array('score_id' => $mba_score_data[0]->score_id));
                            if ($saved) {
                                print($feeder_data->id_transfer.'<br>');
                            }
                        }
                        // var_dump($mba_score_data);
                    }else{
                        // $i++;
                        // var_dump($feeder_data);
                        if (!in_array($mbo_student_data->personal_data_name, $a_student_name)) {
                            array_push($a_student_name, $mbo_student_data->personal_data_name);
                        }
                    }
                }else{
                    print('Mahasiswa tidak ditemukan!');
                }
            }
            // var_dump($a_student_name);
        }else{
            print('eer nl transfer:');
            var_dump($a_dikti_data_transfer);
        }

        if (count($a_student_name) > 0) {
            print('<hr>Mahasiswa yang nilai transfernya belum ada di portal<pre>');
            var_dump($a_student_name);
        }
        // print($i);
    }

    public function checked($s_class_group_id)
    {
        $a_feeder_class = $this->feederapi->post('GetPesertaKelasKuliah', array(
            'filter' => "id_kelas_kuliah = '$s_class_group_id'"
            // 'filter' => "id_registrasi_mahasiswa = '$s_student_id'"
        ));

        print('<pre>');
        print_r($a_feeder_class);
    }

    public function this_transfer_prodi($s_personal_data_id)
    {
        // $s_personal_data_id = '323d2ea2-0009-4416-a27f-c662195a0c1a';
        $mba_student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id));
        $a_prodi = array();
        if ($mba_student_data) {
            foreach ($mba_student_data as $o_student) {
                if (!in_array($o_student->study_program_id, $a_prodi)) {
                    array_push($a_prodi, $o_student->study_program_id);
                }
            }
        }

        if (count($a_prodi) > 1) {
            return true;
        }else{
            return false;
        }
    }

    public function test()
    {
        $a_get_detail_data = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', [
            'filter' => "id_registrasi_mahasiswa = '7bd68e86-d997-481f-8840-63cf58c5d7d4'"
        ]);

        print('<pre>');
        var_dump($a_get_detail_data);
        
        // $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
        //     'filter' => "id_kelas_kuliah = '7ca09ca3-ef1a-4c08-83b7-5d1bb63a633b'"
        // ]);

        // print('<pre>');
        // var_dump($a_get_detail_kelas_kuliah);

        // $mba_score_data = $this->Scm->get_score_data([
		// 	'sc.academic_year_id' => 2019,
		// 	'sc.semester_type_id' => 1,
		// 	// 'sb.subject_id' => 'a353636e-806a-462e-8e84-6dd526da6009'
		// 	// 'sb.subject_id' => '96b1db18-18f4-4703-b11a-904b453f1634'
		// 	// 'sb.subject_id' => 'd9fa7c1d-e3ce-46a9-af91-a33db9ee52f0'
		// 	// 'sb.subject_id' => '676db55a-c15b-4a4d-83ab-731c32063b09'
		// 	'sb.subject_id' => 'a353636e-806a-462e-8e84-6dd526da6009'
        // ]);
        // if ($mba_score_data) {
        //     foreach ($mba_score_data as $o_score) {
        //         print($o_score->student_email.' - '.$o_score->class_group_id.' - '.$o_score->student_id.'<br>');
        //     }
        // }
        
    }

    public function remove_feeder_krs($s_student_id, $s_semester_dikti)
    {
        $a_riwayat_nilai_mahasiswa = $this->feederapi->post('GetRiwayatNilaiMahasiswa', array(
            'filter' => "id_registrasi_mahasiswa = '$s_student_id' AND id_periode = '$s_semester_dikti'"
        ));

        print('<pre>');
        if (($a_riwayat_nilai_mahasiswa->error_code == 0) AND (count($a_riwayat_nilai_mahasiswa->data) > 0)) {
            $a_feeder_data = $a_riwayat_nilai_mahasiswa->data;
            foreach ($a_feeder_data as $o_riwayat_nilai) {
                $remove_student_kelas = $this->feederapi->post('DeletePesertaKelasKuliah', array(
                    'key' => array(
                        'id_kelas_kuliah' => $o_riwayat_nilai->id_kelas,
                        'id_registrasi_mahasiswa' => $s_student_id
                    )
                ));

                print_r($remove_student_kelas);
                print('<br>');
            }
        }

        // print('<pre>');
        // var_dump($a_riwayat_nilai_mahasiswa);
    }

    public function test_score()
    {
        $o_update_nilai_kelas_kuliah = $this->feederapi->post('UpdateNilaiPerkuliahanKelas', array(
            'key' => [
                'id_registrasi_mahasiswa' => '1d7e9e78-ce6b-490a-bf19-e3daa272ded2',
                'id_kelas_kuliah' => '36febd9a-214c-4c2a-8548-6a06511e9394'
            ],
            'record' => [
                'nilai_angka' => '36',
                'nilai_indeks' => '0',
                'nilai_huruf' => 'F'
            ]
        ));

        print('<pre>');print_r($o_update_nilai_kelas_kuliah);
    }

    public function sync_student_score($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mba_student_score = $this->Scm->get_score_data(array(
            'sc.student_id' => $s_student_id,
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id,
            'sc.score_approval' => 'approved'
        ));
        
        print('<pre>');
        // var_dump($s_student_id);exit;

        if ($mba_student_score) {
            $this->remove_feeder_krs($s_student_id, $s_academic_year_id.$s_semester_type_id);
            // var_dump($s_student_id);exit;
            foreach ($mba_student_score as $o_student_score) {
                if($o_student_score->score_approval == 'approved' && in_array($o_student_score->curriculum_subject_type, ['mandatory', 'elective'])) {
                    if (!is_null($o_student_score->class_group_id)) {
                        $a_feeder_class = $this->feederapi->post('GetPesertaKelasKuliah', array(
                            'filter' => "id_registrasi_mahasiswa = '$s_student_id' AND id_kelas_kuliah = '$o_student_score->class_group_id'"
                            // 'filter' => "id_registrasi_mahasiswa = '$s_student_id'"
                        ));

                        // var_dump($a_feeder_class);

                        if ($a_feeder_class->error_code == 0) {
                            // if (count($a_feeder_class->data) > 0) {

                                $a_prep_krs = array(
                                    'id_kelas_kuliah' => $o_student_score->class_group_id,
                                    'id_registrasi_mahasiswa' => $o_student_score->student_id
                                );

                                $o_insert_peserta_kelas_kuliah = $this->feederapi->post('InsertPesertaKelasKuliah', array(
                                    'record' => $a_prep_krs
                                ));

                                if ($o_insert_peserta_kelas_kuliah->error_code == 0) {
                                    $score_sum = intval(round($o_student_score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $grade_point = $this->grades->get_grade_point($score_sum);
                                    $grade = $this->grades->get_grade($score_sum);

                                    $o_update_nilai_kelas_kuliah = $this->feederapi->post('UpdateNilaiPerkuliahanKelas', array(
                                        'key' => [
                                            'id_registrasi_mahasiswa' => $o_student_score->student_id,
                                            'id_kelas_kuliah' => $o_student_score->class_group_id
                                        ],
                                        'record' => [
                                            'nilai_angka' => $score_sum,
                                            'nilai_indeks' => $grade_point,
                                            'nilai_huruf' => $grade
                                        ] 
                                    ));

                                    print_r($o_update_nilai_kelas_kuliah);
                                }else {
                                    print('Error!<br>');
                                    print_r($o_insert_peserta_kelas_kuliah);
                                }
                            // }else{
                            //     $feeder_subject_id = $this->get_id_matkul($score);
                            //     $this->sync_class_student($o_student_score->class_group_id, $feeder_subject_id);
                            // }
                        }else{
                            print('Error!<br>');
                            print_r($a_feeder_class);
                        }
                    }else{
                        print('<hr>Class_group_id is null!!');
                        print('<pre>');
                        var_dump($o_student_score);
                        exit;
                    }
                }
            }
        }else{
            print('score not found');
        }
    }

    public function student_sync($s_academic_year_id = false, $s_semester_type_id = false, $b_is_ajax_request = false)
    {
        // print('mohon maaf, warung tutup!');exit;
        $this->s_stage = 'student_sync';
        if ($b_is_ajax_request) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = false;
            $s_study_program_id = $this->input->post('study_program_id');
            $status_filter = false;

            $a_filter = array(
                'ds.academic_year_id' => $s_academic_year_id,
                'ds.student_not_reported_to_feeder' => NULL,
                'ds.study_program_id' => $s_study_program_id
            );

            if ($s_academic_year_id == 'all') {
                $this->print_result('error', 'Please select academic year for syncronize to feeder!');
                exit;
            }else if ($s_study_program_id == 'all') {
                unset($a_filter['ds.study_program_id']);
            }

            if ($this->input->post('student_id')) {
                $a_filter['ds.student_id'] = $this->input->post('student_id');
            }
            // $this->print_result('', implode(", ",$a_filter));
            // $this->print_result('', implode(", ",array_keys($a_filter)));exit;
            
            $mba_student_data = $this->Stm->get_student_filtered($a_filter, $status_filter);
        }else{
            // $mba_student_data = $this->Stm->get_student_filtered(array('student_not_reported_to_feeder' => NULL), array('active', 'inactive', 'onleave', 'dropout', 'graduated'));
            $mba_student_data = $this->Stm->get_student_filtered(array('student_not_reported_to_feeder' => NULL), array('graduated'));
        }
        print('<pre>');
        var_dump($mba_student_data);exit;
        
        $this->print_result('', '---Sinkronisasi data mahasiswa---');

        // $a_status_allowed = array('active', 'inactive', 'onleave', 'dropout', 'graduated');
        $a_status_allowed = array('graduated');
        if (($mba_student_data) AND ($s_academic_year_id)) {
            $this->i_total_data = count($mba_student_data);
            $i_current_process = 0;
            foreach ($mba_student_data as $student) {
                $i_current_process++;

                if (in_array($student->student_status, $a_status_allowed)) {
                    $s_student_id = $student->student_id;
                    $s_id_mahasiswa = $student->personal_data_id;

                    // if ($this_transfer_prodi) {
                    //     $mbo_student_semester_data = $this->Smm->get_student_start_semester($s_student_id);
                    // }else{
                    //     $mbo_student_semester_data = $this->Smm->get_student_start_semester(false, array(
                    //         'ds.personal_data_id' => $student->personal_data_id
                    //     ));
                    // }
                    $mbo_student_semester_data = $this->Smm->get_student_start_semester(false, array(
                        'st.student_id' => $student->student_id
                    ));
                    // $this->print_result('', $student->personal_data_name.' _ '.$mbo_student_semester_data->academic_year_id);
                    if (($mbo_student_semester_data) AND ($mbo_student_semester_data->academic_year_id == $s_academic_year_id)) {
                        $b_process = true;
                        // if (($s_semester_type_id) AND ($mbo_student_semester_data->semester_type_id != $s_semester_type_id)) {
                        //     $b_process = false;
                        // }

                        if ($b_process) {
                            $a_mahasiswa_feeder = $this->feederapi->post('GetListMahasiswa', array(
                                // 'filter' => "nama_mahasiswa = '$student->personal_data_name' AND tempat_lahir = '$student->personal_data_place_of_birth' AND tanggal_lahir = '$student->personal_data_date_of_birth' AND nama_ibu_kandung = '$student->personal_data_mother_maiden_name'"
                                'filter' => "id_mahasiswa = '$s_id_mahasiswa'"
                            ));
        
                            $b_biodata_saved = false;
        
                            if ($a_mahasiswa_feeder->error_code != '0') {
                                $a_result = array('code' => 1, 'message' => 'Error sinkronisasi: '.$a_mahasiswa_feeder->error_desc, 'process' => $a_mahasiswa_feeder);
                                print('<pre>');var_dump($a_result);exit;
                            }else if (count($a_mahasiswa_feeder->data) == 0) {
                                if ($student->student_status != 'resign') {
                                    $post_biodata = $this->post_insert_biodata($s_student_id);
                                    if ($post_biodata['code'] == 0) {
                                        $s_id_mahasiswa = $post_biodata['id_mahasiswa'];
                                        $b_biodata_saved = true;
                                        $this->print_result('success', $i_current_process.'Sinkronisasi insert biodata mahasiswa '.$student->personal_data_name.' berhasil', $i_current_process);
                                    }else{
                                        $this->print_result('error', $i_current_process.'Sinkronisasi insert biodata mahasiswa '.$student->personal_data_name.' gagal: id_mahasiswa ('.$s_id_mahasiswa.') - '.$post_biodata['message'], $i_current_process);
                                    }
                                }else{
                                    $b_biodata_saved = true;
                                    $this->print_result('', $i_current_process.'Sinkronisasi insert biodata mahasiswa '.$student->personal_data_name.': Biodata mahasiswa tidak dilaporkan karena status mahasiswa resign', $i_current_process);
                                }
                            }else{
                                $data = $a_mahasiswa_feeder->data;
                                if ($data[0]->id_mahasiswa != $s_id_mahasiswa) {
                                    $this->Pdm->update_personal_data(array('personal_data_id' => $data[0]->id_mahasiswa), $student->personal_data_id);
                                    $s_id_mahasiswa = $data[0]->id_mahasiswa;
                                }else{
                                    $s_id_mahasiswa = $student->personal_data_id;
                                }

                                if ($student->student_status != 'resign') {
                                    $post_biodata = $this->post_insert_biodata($s_student_id, $s_id_mahasiswa);
                                    if ($post_biodata['code'] == 0) {
                                        $b_biodata_saved = true;
                                        $this->print_result('success', $i_current_process.'Sinkronisasi update biodata mahasiswa '.$student->personal_data_name.' berhasil', $i_current_process);
                                    }else{
                                        $this->print_result('error', $i_current_process.'Sinkronisasi update biodata mahasiswa '.$student->personal_data_name.' error : id_mahasiswa ('.$s_id_mahasiswa.') - '.$post_biodata['message'], $i_current_process);
                                    }
                                }else{
                                    $b_biodata_saved = true;
                                    $this->print_result('', $i_current_process.'Sinkronisasi update biodata mahasiswa '.$student->personal_data_name.': Biodata mahasiswa tidak dilaporkan karena status mahasiswa resign', $i_current_process);
                                }
                            }
        
                            if ($b_biodata_saved) {
                                $post_riwayat = $this->post_insert_riwayat_pendidikan($s_student_id);
                                if ($post_riwayat['code'] > 0) {
                                    $this->print_result('error', 'Sinkronisasi riwayat pendidikan '.$student->personal_data_name.' gagal: id_mahasiswa ('.$s_id_mahasiswa.'): "'.$post_riwayat['message'].'"', $i_current_process);
                                }else{
                                    $this->print_result('success', 'Sinkronisasi riwayat pendidikan mahasiswa berhasil: Data '.$student->personal_data_name, $i_current_process);
                                    $s_student_id = $post_riwayat['id_registrasi_mahasiswa'];

                                    $this_transfer_prodi = $this->this_transfer_prodi($s_id_mahasiswa);
        
                                    if (($student->student_type == 'transfer') OR ($this_transfer_prodi)) {
                                        $save_transfer = $this->post_insert_nilai_transfer(array(
                                            'id_mahasiswa' => $s_id_mahasiswa,
                                            'id_registrasi_mahasiswa' => $s_student_id,
                                            'transfer_prodi' => $this_transfer_prodi
                                        ));
        
                                        if ($save_transfer['code'] > 0) {
                                            $this->print_result('error', 'Sinkronisasi nilai transfer '.$student->personal_data_name.' gagal: id_mahasiswa ('.$s_id_mahasiswa.'): " '.$save_transfer['message'].'"', $i_current_process);
                                        }else{
                                            $this->print_result('success', 'Sinkronisasi nilai transfer mahasiswa berhasil: Data '.$student->personal_data_name, $i_current_process);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $this->print_result('', $i_current_process.'Sinkronisasi biodata mahasiswa tidak dilakukan '.$student->personal_data_name.': Student semester tidak sesuai!', $i_current_process);
                    }
                }else{
                    $this->print_result('', $i_current_process.'Sinkronisasi biodata mahasiswa tidak dilakukan '.$student->personal_data_name.': Status mahasiswa tidak diijinkan!', $i_current_process);
                }
            }
        }
    }

    public function remove_transfer_student($s_id_registrasi_mahasiswa)
    {
        $a_feeder_nilai_transfer = $this->feederapi->post('GetNilaiTransferPendidikanMahasiswa', array(
            'filter' => "id_registrasi_mahasiswa = '$s_id_registrasi_mahasiswa'"
        ));
        if ($a_feeder_nilai_transfer->error_code != '0') {
            print('Error get data from feeder: ');
            print('<pre>');var_dump($a_feeder_nilai_transfer);exit;
        }else if (count($a_feeder_nilai_transfer->data) > 0) {
            $a_feeder_data = $a_feeder_nilai_transfer->data;
            foreach ($a_feeder_data as $feeder_data) {
                $a_feeder_remove_nilai_transfer = $this->feederapi->post('DeleteNilaiTransferPendidikanMahasiswa', array(
                    'key' => array(
                        'id_transfer' => $feeder_data->id_transfer
                    )
                ));

                if ($a_feeder_remove_nilai_transfer->error_code != '0') {
                    print('Error get data from feeder: ');
                    var_dump($a_feeder_remove_nilai_transfer);exit;
                }
            }
        }
    }

    public function cek()
    {
        $a_feeder_cek = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
            'filter' => "id_mahasiswa = '7c138e87-5eec-418d-a1f7-ad21c4268de3'"
        ));

        print('<pre>');
        var_dump($a_feeder_cek);
    }

    public function post_insert_nilai_transfer($a_data = false)
    {
        $s_academic_year_id = "20232";
        $a_data = array(
            'id_mahasiswa' => '7c138e87-5eec-418d-a1f7-ad21c4268de3',
            'id_registrasi_mahasiswa' => 'e10ee92b-9708-4b26-8239-b2e587c20a5b',
            'transfer_prodi' => false
        );

        if ($a_data['transfer_prodi']) {
            $mba_student_transfer_credit = $this->Scm->get_score_data(array(
                'st.personal_data_id' => $a_data['id_mahasiswa'],
                'st.student_status' => 'resign',
                'sc.score_approval' => 'approved',
                'sc.score_grade != ' => 'F',
                'curs.curriculum_subject_type != ' => 'extracurricular'
            ));
        }else{
            $mba_student_transfer_credit = $this->Scm->get_score_data(array(
                'sc.student_id' => $a_data['id_registrasi_mahasiswa'],
                'sc.semester_type_id' => '5'
            ));
        }

        $this->remove_transfer_student($a_data['id_registrasi_mahasiswa']);
        $mbs_error_message = false;
        if ($mba_student_transfer_credit) {
            print('<pre>');
            $a_subject_not_found = array();
            foreach ($mba_student_transfer_credit as $score) {
                if ($score->curriculum_subject_type != 'extracurricular') {
                    print('nilai transfer '.$score->subject_name.'<br>');
                    $feeder_subject_id = $this->_check_matkul($score);
                    
                    if ($feeder_subject_id) {
                        $a_feeder_nilai_transfer = $this->feederapi->post('GetNilaiTransferPendidikanMahasiswa', array(
                            "filter" => "id_registrasi_mahasiswa = '{$a_data['id_registrasi_mahasiswa']}' AND id_matkul = '$feeder_subject_id'"
                        ));
                        $a_original_subject = (!empty($score->original_subject)) ? explode('|', $score->original_subject) : [];
                        $s_original_subject_code = ((is_array($a_original_subject)) AND (count($a_original_subject) > 1)) ? $a_original_subject[0] : '-';
                        $s_original_subject_name = ((is_array($a_original_subject)) AND (count($a_original_subject) > 0)) ? $a_original_subject[count($a_original_subject) - 1] : $score->subject_name;
                        // $s_original_subject_name = '';
                        $s_original_subject_credit = $score->original_credit;
                        
                        $a_record_feeder = array(
                            'id_registrasi_mahasiswa' => $a_data['id_registrasi_mahasiswa'],
                            'id_matkul' => $score->subject_id,
                            'kode_mata_kuliah_asal' => $s_original_subject_code,
                            'nama_mata_kuliah_asal' => $s_original_subject_name,
                            'sks_mata_kuliah_asal' => $s_original_subject_credit,
                            'nilai_huruf_asal' => $score->score_grade,
                            'sks_mata_kuliah_diakui' => $score->curriculum_subject_credit,
                            'nilai_huruf_diakui' => $score->score_grade,
                            'nilai_angka_diakui' => $score->score_grade_point,
                            'id_semester' => $s_academic_year_id,
                            // 'id_aktivitas' => NULL
                        );
        
                        if ($a_feeder_nilai_transfer->error_code != '0') {
                            print('Error get data from feeder: ');
                            var_dump($a_feeder_nilai_transfer);exit;
                        }else if (count($a_feeder_nilai_transfer->data) == 0) {
                            # insert
                            $a_result = $this->feederapi->post('InsertNilaiTransferPendidikanMahasiswa', array(
                                'record' => $a_record_feeder
                            ));

                            if ($a_result->error_code != '0') {
                                print('Error processing data to feeder: <pre>');
                                var_dump($a_result);exit;
                            }else{
                                $o_feeder_nilai_transfer_data = $a_result->data;
                                $s_id_nilai_transfer = $o_feeder_nilai_transfer_data->id_transfer;

                                if ($s_id_nilai_transfer != $score->score_id) {
                                    $this->Scm->save_data(array('score_id' => $s_id_nilai_transfer), array('score_id' => $score->score_id));
                                }
                            }
                        }else{
                            # update
                            $o_feeder_nilai_transfer_data = $a_feeder_nilai_transfer->data[0];
                            $s_id_nilai_transfer = $o_feeder_nilai_transfer_data->id_transfer;
                            $a_result = $this->feederapi->post('UpdateNilaiTransferPendidikanMahasiswa', array(
                                'key' => array(
                                    'id_transfer' => $o_feeder_nilai_transfer_data->id_transfer
                                ),
                                'record' => $a_record_feeder
                            ));

                            if ($a_result->error_code != '0') {
                                print('Error processing data to feeder: <pre>');
                                var_dump($a_result);exit;
                            }

                            if ($s_id_nilai_transfer != $score->score_id) {
                                $this->Scm->save_data(array('score_id' => $s_id_nilai_transfer), array('score_id' => $score->score_id));
                            }
                        }

                        // var_dump($a_result);
                    }else{
                        if (!in_array($score->subject_name, $a_subject_not_found)) {
                            array_push($a_subject_not_found, $score->subject_name);
                        }
                    }
                }
            }

            if (count($a_subject_not_found) > 0) {
                $a_return = array('code' => 1, 'message' => 'Subject '.implode(', ', $a_subject_not_found).' in score_id '.$score->score_id.' not found in feeder!');
            }else{
                $a_return = array('code' => 0);
            }
        }else{
            $a_return = array('code' => 1, 'message' => 'Transfer value has not been inputted!');
        }

        // return $a_return;
        // var_dump('transfer result: '.$a_return);
    }

    public function get_id_matkul($o_score_data)
    {
        $mbo_subject_data = $this->Sbm->get_subject_filtered(array('rs.subject_id' => $o_score_data->subject_id))[0];
        $s_study_program_id = (is_null($mbo_subject_data->study_program_main_id)) ? $mbo_subject_data->study_program_id : $mbo_subject_data->study_program_main_id;

        $a_feeder_data = $this->feederapi->post('GetListMataKuliah', array(
            "filter" => "nama_mata_kuliah = '$mbo_subject_data->subject_name' AND sks_mata_kuliah = '$mbo_subject_data->subject_credit' AND id_prodi = '$s_study_program_id'"
        ));

        $s_subject_id = $mbo_subject_data->subject_id;

        if ($a_feeder_data->error_code != '0') {
            print('Error get data from feeder: ');
            var_dump($a_feeder_data);exit;
        }else if (count($a_feeder_data->data) == 0) {
            // return false;
            if(is_null($mbo_subject_data->id_jenis_mata_kuliah)){
                switch($o_score_data->curriculum_subject_type){
                    case 'mandatory':
                        $s_id_jenis_matakuliah = 'A';
                        break;
                        
                    case 'elective':
                        $s_id_jenis_matakuliah = 'B';
                        break;
                }
            }
            else{
                $s_id_jenis_matakuliah = $o_subject_data->id_jenis_mata_kuliah;
            }
            
            $a_subject_data = array(
                'id_prodi' => $s_study_program_id,
                'kode_mata_kuliah' => $mbo_subject_data->subject_code,
                'nama_mata_kuliah' => $mbo_subject_data->subject_name,
                'id_jenis_mata_kuliah' => $s_id_jenis_matakuliah,
                'sks_mata_kuliah' => $mbo_subject_data->subject_credit,
                'sks_tatap_muka' => $mbo_subject_data->subject_credit_tm,
                'sks_praktek' => $mbo_subject_data->subject_credit_p,
                'sks_praktek_lapangan' => $mbo_subject_data->subject_credit_pl,
                'sks_simulasi' => $mbo_subject_data->subject_credit_s
            );

            $feeder_insert_matkul = $this->feederapi->post('InsertMataKuliah', array(
                'record' => $a_subject_data
            ));

            if ($feeder_insert_matkul->error_code != '0') {
                print('Error processing data to feeder: ');
                var_dump($feeder_insert_matkul);exit;
            }else{
                $s_subject_id = $feeder_insert_matkul->id_matkul;
                $this->Sbm->save_subject_data(array('subject_id' => $feeder_insert_matkul->id_matkul), $mbo_subject_data->subject_id);
            }
        }else{
            $feeder_data = $a_feeder_data->data[0];
            if ($mbo_subject_data->subject_id != $feeder_data->id_matkul) {
                $s_subject_id = $feeder_data->id_matkul;
                $this->Sbm->save_subject_data(array('subject_id' => $feeder_data->id_matkul), $mbo_subject_data->subject_id);
            }
        }

        return $s_subject_id;
    }

    private function _check_matkul($o_subject_data)
	{
		$a_get_detail_mata_kuliah = $this->feederapi->post('GetDetailMataKuliah', [
			'filter' => "id_matkul = '{$o_subject_data->subject_id}'"
		]);
		// print('<pre>');var_dump($a_get_detail_mata_kuliah);exit;
		
		if($a_get_detail_mata_kuliah->error_code == 0 && count($a_get_detail_mata_kuliah->data) >= 1){
			return $o_subject_data->subject_id;
		}
		else{
			if(in_array($o_subject_data->curriculum_subject_type, ['mandatory', 'elective'])){
				if(is_null($o_subject_data->id_jenis_mata_kuliah)){
					switch($o_subject_data->curriculum_subject_type){
						case 'mandatory':
							$s_id_jenis_matakuliah = 'A';
							break;
							
						case 'elective':
							$s_id_jenis_matakuliah = 'B';
							break;
					}
				}
				else{
					$s_id_jenis_matakuliah = $o_subject_data->id_jenis_mata_kuliah;
				}
                $mba_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_subject_data->subject_study_program_id]);
				
				$a_data_mata_kuliah = [
					'id_prodi' => (is_null($mba_prodi[0]->study_program_main_id)) ? $mba_prodi[0]->study_program_id : $mba_prodi[0]->study_program_main_id,
					'kode_mata_kuliah' => $o_subject_data->subject_code,
					'nama_mata_kuliah' => $o_subject_data->subject_name,
					'id_jenis_mata_kuliah' => $s_id_jenis_matakuliah,
					'sks_mata_kuliah' => $o_subject_data->curriculum_subject_credit,
					'sks_tatap_muka' => $o_subject_data->subject_credit_tm,
					'sks_praktek' => $o_subject_data->subject_credit_p,
					'sks_praktek_lapangan' => $o_subject_data->subject_credit_pl,
					'sks_simulasi' => $o_subject_data->subject_credit_s
				];
				
				$o_insert_mata_kuliah = $this->feederapi->post('InsertMataKuliah', [
					'record' => $a_data_mata_kuliah
				]);
	
				if($o_insert_mata_kuliah->error_code == 0){
					// print('<pre>');var_dump($o_insert_mata_kuliah);exit;
					$s_id_matkul = $o_insert_mata_kuliah->data->id_matkul;
					
					if(!$this->Sbj->get_subject_filtered([
						'subject_id' => $s_id_matkul
					])){
						$this->Sbj->save_subject_data([
							'subject_id' => $s_id_matkul
						], $o_subject_data->subject_id);
					}
					return $s_id_matkul;
				}
				else{
					$a_get_detail_mata_kuliah = $this->feederapi->post('GetDetailMataKuliah', [
						'filter' => "kode_mata_kuliah = '{$o_subject_data->subject_code}' AND nama_mata_kuliah = '{$o_subject_data->subject_name}'"
					]);
					
					if($a_get_detail_mata_kuliah->error_code == 0 && count($a_get_detail_mata_kuliah->data) >= 1){
						$s_id_matkul = $a_get_detail_mata_kuliah->data[0]->id_matkul;
						$this->Sbj->save_subject_data([
							'subject_id' => $s_id_matkul
						], $o_subject_data->subject_id);
						return $s_id_matkul;
					}else{
						print('004<br>');
						var_dump($a_get_detail_mata_kuliah);
					}
				}
			}
		}
	}

    // fungsi ini digunakan hanya sekali atau kalau ada keperluan
    // public function delete_mahasiswa_lulus_do()
	// {
	// 	$mba_mahasiswa_lulus = $this->General->get_where('dt_student', [
	// 		'student_status' => 'graduated',
	// 		'student_mark_completed_defense' => 1
	// 	]);

    //     print('<pre>');
    //     $i_count = 0;
	// 	foreach ($mba_mahasiswa_lulus as $o_student) {
    //         $a_feeder_list_perkuliahan_data = $this->feederapi->post('GetDetailMahasiswaLulusDO', array(
    //             'filter' => "id_registrasi_mahasiswa = '$o_student->student_id'"
    //         ));

    //         if (($a_feeder_list_perkuliahan_data->error_code == 0) AND (count($a_feeder_list_perkuliahan_data->data) > 0)) {
    //             $a_delete_mahasiswa_lulus = $this->feederapi->post('DeleteMahasiswaLulusDO', [
    //                 'key' => [
    //                     'id_registrasi_mahasiswa' => $o_student->student_id
    //                 ]
    //             ]);

    //             if ($a_delete_mahasiswa_lulus->error_code == 0) {
    //                 print($o_student->student_email);
    //                 print('<br>');
    //             }else{
    //                 var_dump($a_delete_mahasiswa_lulus);
    //             }

    //             $i_count++;
    //         }
    //     }
        
    //     print('<h1>'.$i_count.'</h1>');
		
	// 	// print('<pre>');
	// 	// var_dump($mba_mahasiswa_lulus);exit;
	// }

    function getkeluar() {
        $forlapkeluar_result = $this->feederapi->post("GetListMahasiswaLulusDO", [
            'filter' => "id_jenis_keluar = '1'"
        ]);
        print('<table style="width: 100%;" border="1">');
        print('<tr>');
        print('<td>Nama</td>');
        print('<td>NIM</td>');
        print('<td>Angkatan</td>');
        print('<td>Prodi</td>');
        // print('<td>Periode Lulus</td>');
        print('<td>Tanggal Keluar</td>');
        print('<td>Tanggal SK Yudisium</td>');
        print('<td>Periode Keluar</td>');
        print('<td>Periode Perkuliahan</td>');
        print('<td>Status Perkuliahan</td>');
        print('</tr>');

        $forlapdata = $forlapkeluar_result->data;
        foreach ($forlapdata as $o_forlap) {
            $mba_student_data = $this->Stm->get_student_filtered([
                'ds.student_id' => $o_forlap->id_registrasi_mahasiswa,
                'ds.student_status' => 'graduated'
            ]);

            if (!$mba_student_data) {
                print('<pre>');var_dump($o_forlap);exit;
            }
            $o_student = $mba_student_data[0];

            $s_valid = "";
            $forlapsemester = $this->feederapi->post("GetAktivitasKuliahMahasiswa", [
                'filter' => "id_registrasi_mahasiswa = '".$o_forlap->id_registrasi_mahasiswa."' AND id_semester > '".$o_forlap->id_periode_keluar."'"
            ]);
            if ($forlapsemester->error_code != '0') {
                print('Error get data from feeder: ');
                var_dump($forlapsemester);exit;
            }
            else if (count($forlapsemester->data) > 0) {
                $forlapsemesterdata = $forlapsemester->data;
                $a_valid = [];
                foreach ($forlapsemesterdata as $o_semester) {
                    // array_push($a_valid, $o_semester->id_semester."-".$o_semester->id_status_mahasiswa);
                    print('<tr>');
                    print('<td>'.$o_student->personal_data_name.'</td>');
                    print('<td>'.$o_student->student_number.'</td>');
                    print('<td>'.$o_forlap->angkatan.'</td>');
                    print('<td>'.$o_student->study_program_abbreviation.'</td>');
                    print('<td>'.$o_forlap->tanggal_keluar.'</td>');
                    print('<td>'.$o_forlap->tgl_sk_yudisium.'</td>');
                    print('<td>'.$o_forlap->id_periode_keluar.'</td>');
                    print('<td>'.$o_semester->id_semester.'</td>');
                    print('<td>'.$o_semester->id_status_mahasiswa.'</td>');
                    print('</tr>');
                }
                $s_valid = implode("<br>", $a_valid);
            }
            
            // print('<tr>');
            // print('<td>'.$o_student->personal_data_name.'</td>');
            // print('<td>'.$o_student->student_number.'</td>');
            // print('<td>'.$o_forlap->angkatan.'</td>');
            // print('<td>'.$o_student->study_program_abbreviation.'</td>');
            // print('<td>'.$o_forlap->tanggal_keluar.'</td>');
            // print('<td>'.$o_forlap->tgl_sk_yudisium.'</td>');
            // print('<td>'.$o_forlap->id_periode_keluar.'</td>');
            // print('<td>'.$s_valid.'</td>');
            // print('</tr>');
        }
        // print('<pre>');var_dump($forlapkeluar_result);exit;
    }

    function get_student_graduated() {
        $s_prodi_id = '6266e096-63ad-4b77-82b0-17216155a70e';
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.student_status' => 'graduated',
            // 'ds.study_program_id' => $s_prodi_id
        ]);
        if ($mba_student_data) {
            print('<table style="width: 100%;" border="1">');
            print('<tr>');
            print('<td>Nama</td>');
            print('<td>NIM</td>');
            print('<td>Angkatan</td>');
            print('<td>Prodi</td>');
            // print('<td>Periode Lulus</td>');
            print('<td>Tanggal Keluar</td>');
            print('<td>Tanggal SK Yudisium</td>');
            print('<td>Periode Keluar</td>');
            print('<td>Terakhir Perkuliahan</td>');
            // print('<td>judul_skripsi</td>');
            print('</tr>');
            
            foreach ($mba_student_data as $o_student) {
                $forlapkeluar_result = $this->feederapi->post("GetDetailMahasiswaLulusDO", [
                    'filter' => "id_registrasi_mahasiswa = '".$o_student->student_id."'"
                ]);

                // print($o_student->personal_data_name);
                if ($forlapkeluar_result->error_code != '0') {
                    print('Error get data from feeder: ');
                    var_dump($forlapkeluar_result);exit;
                }else if (count($forlapkeluar_result->data) > 0) {
                    $forlapdata = $forlapkeluar_result->data[0];
                    $s_nama_semester = "";
                    $last_id_semester = "";
                    $last_nama_semester = "";
                    $semesterforlap = $this->feederapi->post("GetSemester", [
                        'filter' => "id_semester = '".$forlapdata->id_periode_keluar."'"
                    ]);
                    $forlapkuliah_result = $this->feederapi->post("GetListPerkuliahanMahasiswa", [
                        'filter' => "id_registrasi_mahasiswa = '".$o_student->student_id."'",
                        'order' => "id_semester DESC"
                    ]);

                    if ($semesterforlap->error_code != '0') {
                        print('Error get data from feeder: ');
                        var_dump($semesterforlap);exit;
                    }else if (count($semesterforlap->data) > 0) {
                        $forlapsemesterdata = $semesterforlap->data[0];
                        $s_nama_semester = $forlapsemesterdata->nama_semester;
                    }

                    if ($forlapkuliah_result->error_code != '0') {
                        print('Error get data from feeder: ');
                        var_dump($forlapkuliah_result);exit;
                    }else if (count($forlapkuliah_result->data) > 0) {
                        $forlaplast_semester = $forlapkuliah_result->data[0];
                        $last_id_semester = $forlaplast_semester->id_semester;
                        $last_nama_semester = $forlaplast_semester->nama_semester;
                    }

                    // print(" ".$forlapdata->id_periode_keluar."/".$s_nama_semester);
                    print('<tr>');
                    print('<td>'.$o_student->personal_data_name.'</td>');
                    print('<td>'.$o_student->student_number.'</td>');
                    print('<td>'.$forlapdata->angkatan.'</td>');
                    print('<td>'.$o_student->study_program_abbreviation.'</td>');
                    print('<td>'.$forlapdata->tanggal_keluar.'</td>');
                    print('<td>'.$forlapdata->tanggal_sk_yudisium.'</td>');
                    print('<td>'.$s_nama_semester.'</td>');
                    print('<td>'.$last_nama_semester.'</td>');
                    print('</tr>');
                }

                // print('<br>');

                
                // tanggal_keluar
                // tanggal_sk_yudisium
                // id_periode_keluar
                // nama_semester

                // print('<pre>');var_dump($forlapkeluar_result);exit;
            }
            print('</table>');
        }
        // print('<pre>');var_dump($mba_student_data);exit;
    }

    function sync_student_graduated($s_graduate_year_id) {
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.graduated_year_id' => $s_graduate_year_id
        ]);
        if ($mba_student_data) {
            $i_row = 2;
            print('<table style="width: 100%;" border="1">');
            print('<tr>');
            print('<td>Nama</td>');
            print('<td>id_jenis_keluar</td>');
            print('<td>tanggal_keluar</td>');
            print('<td>id_periode_keluar</td>');
            print('<td>nomor_sk_yudisium</td>');
            print('<td>tanggal_sk_yudisium</td>');
            print('<td>ipk</td>');
            print('<td>nomor_ijazah</td>');
            print('<td>judul_skripsi</td>');
            print('</tr>');
            
            foreach ($mba_student_data as $o_student) {
                // if ($o_student->student_id != 'c1801957-f93d-4dbe-9c7e-96c698cecee0') {
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
                    
                    $a_feeder_data = [
                        // 'id_registrasi_mahasiswa' => $o_student->student_id,
                        'id_jenis_keluar' => '1',
                        'tanggal_keluar' => $o_student->student_date_graduated,
                        'id_periode_keluar' => '20222',
                        'nomor_sk_yudisium' => 'SK/REC/1934/IULI/IX/2023',
                        'tanggal_sk_yudisium' => '2023-09-22',
                        'ipk' => number_format(doubleval($o_student->student_last_gpa), 2),
                        'nomor_ijazah' => $o_student->student_pin_number,
                        'judul_skripsi' => $o_student->student_thesis_title
                    ];
                    
                    $a_feeder_list_perkuliahan_data = $this->feederapi->post('GetDetailMahasiswaLulusDO', array(
                        'filter' => "id_registrasi_mahasiswa = '$o_student->student_id'"
                    ));
    
                    if ($a_feeder_list_perkuliahan_data->error_code != '0') {
                        print('Error get data from feeder: ');
                        var_dump($a_feeder_list_perkuliahan_data);exit;
                    }else if (count($a_feeder_list_perkuliahan_data->data) == 0) {
                        // print('insert<br>');
                        $a_feeder_data['id_registrasi_mahasiswa'] = $o_student->student_id;
                        $a_result = $this->feederapi->post('InsertMahasiswaLulusDO', array(
                            'record' => $a_feeder_data
                        ));
                    }else{
                        // print('update<br>');
                        $a_result = $this->feederapi->post('UpdateMahasiswaLulusDO', array(
                            'key' => array(
                                'id_registrasi_mahasiswa' => $o_student->student_id
                            ),
                            'record' => $a_feeder_data
                        ));
                    }
    
                    if ($a_result->error_code == 0) {
                        // print($o_student->personal_data_name.' Lulus<br>');
                        print('<tr>');
                        print('<td>'.$o_student->personal_data_name.'</td>');
                        print('<td>'.$a_feeder_data['id_jenis_keluar'].'</td>');
                        print('<td>'.$a_feeder_data['tanggal_keluar'].'</td>');
                        print('<td>'.$a_feeder_data['id_periode_keluar'].'</td>');
                        print('<td>'.$a_feeder_data['nomor_sk_yudisium'].'</td>');
                        print('<td>'.$a_feeder_data['tanggal_sk_yudisium'].'</td>');
                        print('<td>'.$a_feeder_data['ipk'].'</td>');
                        print('<td>'.$a_feeder_data['nomor_ijazah'].'</td>');
                        print('<td>'.$a_feeder_data['judul_skripsi'].'</td>');
                        print('</tr>');
                    }else{
                        print('<pre>');
                        var_dump($a_result);exit;
                        $a_return = array('code' => 1, 'message' => $a_result->error_desc);
                    }
                // }
            }
            print('</table>');
        }
    }

    public function post_mahasiswa_lulus_do($a_data = false)
    {
        $data = $this->Smm->get_student_semester(array(
            'dss.student_id' => '00e96da8-37b9-4c04-9a59-fa95a3baab4f',
            'dss.academic_year_id' => '2020',
            'dss.semester_type_id' => '2'
        ));
        // print('<pre>');
        // var_dump($data);exit;

        // $a_data = array(
        //     'id_mahasiswa' => '9300bea7-0d15-4fe1-b3c5-4f52ebd0853f',
        //     'academic_year_id' => '2020',
        //     'semester_type_id' => '2',
        //     'id_semester' => '20202',
        //     'id_registrasi_mahasiswa' => '00e96da8-37b9-4c04-9a59-fa95a3baab4f',
        //     'student_semester_data' => $data[0]
        // );

        $o_student_semester_data = $a_data['student_semester_data'];
        $s_id_reg_mahasiswa = $a_data['id_registrasi_mahasiswa'];
        $s_id_mahasiswa = $a_data['id_mahasiswa'];
        $a_data_feeder = $this->feederapi->post('GetDetailMahasiswaLulusDO', array(
            'filter' => "id_registrasi_mahasiswa = '$s_id_reg_mahasiswa'"
        ));

        if ($a_data_feeder->error_code == '0') {
            $a_feeder_data = array(
                'id_periode_keluar' => $a_data['id_semester']
            );
            
            switch ($o_student_semester_data->student_semester_status) {
                case 'graduated':
                    $a_feeder_data['id_jenis_keluar'] = '1';
                    $a_feeder_data['tanggal_keluar'] = (is_null($o_student_semester_data->student_date_graduated)) ? NULL : $o_student_semester_data->student_date_graduated;
                    break;
                
                case 'resign':
                    $s_resign_date = $o_student_semester_data->student_date_resign;
                    if (is_null($s_resign_date)) {
                        $mbo_semester_data = $this->Smm->get_semester_setting(array(
                            'dss.academic_year_id' => $a_data['academic_year_id'],
                            'dss.semester_type_id' => $a_data['semester_type_id']
                        ))[0];
                        $s_resign_date = $mbo_semester_data->semester_start_date;
                    }
                    $a_feeder_data['id_jenis_keluar'] = '4';
                    $a_feeder_data['tanggal_keluar'] = $s_resign_date;
                    $a_feeder_data['keterangan'] = $o_student_semester_data->student_resign_note;
                    break;

                case 'dropout':
                    $a_feeder_data['id_jenis_keluar'] = '3';
                    $a_feeder_data['tanggal_keluar'] = $o_student_semester_data->semester_timestamp;
                    break;

                default:
                    break;
            }

            $a_feeder_list_perkuliahan_data = $this->feederapi->post('GetDetailMahasiswaLulusDO', array(
                'filter' => "id_registrasi_mahasiswa = '$s_id_reg_mahasiswa'"
            ));
            // print('<pre>');var_dump($a_feeder_list_perkuliahan_data);exit;

            if ($a_feeder_list_perkuliahan_data->error_code != '0') {
                print('Error get data from feeder: ');
                var_dump($a_feeder_list_perkuliahan_data);exit;
            }else if (count($a_feeder_list_perkuliahan_data->data) == 0) {
                // print('insert<br>');
                $a_feeder_data['id_registrasi_mahasiswa'] = $s_id_reg_mahasiswa;
                $a_result = $this->feederapi->post('InsertMahasiswaLulusDO', array(
                    'record' => $a_feeder_data
                ));
            }else{
                // print('update<br>');
                $a_result = $this->feederapi->post('UpdateMahasiswaLulusDO', array(
                    'key' => array(
                        'id_registrasi_mahasiswa' => $s_id_reg_mahasiswa
                    ),
                    'record' => $a_feeder_data
                ));
            }
            
            if ($a_result->error_code == 0) {
                $a_return = array('code' => 0);
            }else{
                print('<pre>');
                var_dump($a_result);exit;
                $a_return = array('code' => 1, 'message' => $a_result->error_desc);
            }
        }else{
            $a_return = array('code' => 1, 'message' => $a_data_feeder->error_desc);
        }
        // print('<pre>');
        // var_dump($a_return);
        return $a_return;
    }

    public function strlen()
    {
        $s_string = 'e15bdbf1-548d-42d7-b13f-bf891eb67311';
        print(strlen($s_string));
    }

    public function sync_perkuliahan_mahasiswa($s_academic_year_id, $s_semester_type_id, $s_study_program_id)
    {
        $a_student_status = ['active', 'inactive', 'onleave'];
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.student_status' => 'active',
            'ds.study_program_id' => $s_study_program_id
        ]);

        if ($mba_student_data) {
            foreach ($mba_student_data as $o_student) {
                $this->post_insert_perkuliahan_mahasiswa([
                    'id_mahasiswa' => $o_student->personal_data_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'id_semester' => $s_academic_year_id.$s_semester_type_id,
                    'id_registrasi_mahasiswa' => $o_student->student_id
                ]);
            }
        }
        else {
            print('student not found!');
        }

        print('<pre>');
        var_dump(count($mba_student_data));exit;
    }

    public function post_insert_perkuliahan_mahasiswa($a_data = false)
    {
        // adaw (MEE/2018) 
        // $a_data = array(
        //     'id_mahasiswa' => '9ba9d669-3eb4-4187-8adf-38cf9cbc4866',
        //     'academic_year_id' => 2022,
        //     'semester_type_id' => 2,
        //     'id_semester' => '20222',
        //     'id_registrasi_mahasiswa' => 'ce5e90e9-c5ab-4f21-8b1b-efe1babbbb6f'
        // );

        $s_id_registrasi_mahasiswa = $a_data['id_registrasi_mahasiswa'];
        $s_semester_dikti = $a_data['id_semester'];
        
        $mbo_student_semester_data = $this->Smm->get_student_semester(array(
            'dss.student_id' => $s_id_registrasi_mahasiswa,
            'dss.academic_year_id' => $a_data['academic_year_id'],
            'dss.semester_type_id' => $a_data['semester_type_id']
        ))[0];
        // print('<pre>');var_dump($mbo_student_semester_data);exit;
        if ($mbo_student_semester_data) {
            $o_student = $this->Stm->get_student_filtered(['ds.student_id' => $s_id_registrasi_mahasiswa])[0];
            $a_feeder_list_riwayat_pendidikan = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
                'filter' => "id_registrasi_mahasiswa = '$s_id_registrasi_mahasiswa'"
            ));

            $mba_semester_type_in = (in_array($a_data['semester_type_id'], [7,8])) ? [7,8] : [$a_data['semester_type_id']];
            if (($a_feeder_list_riwayat_pendidikan->error_code == '0') AND (count($a_feeder_list_riwayat_pendidikan->data) > 0)) {
                $o_data_feeder = $a_feeder_list_riwayat_pendidikan->data[0];
                // if (!is_null($o_data_feeder->id_jenis_keluar)) {
                //     print('<pre>');
                //     var_dump($o_data_feeder);exit;
                // }
                // if (count($o_data_feeder->id_jenis_keluar) == 0) {
                if ((is_null($o_data_feeder->id_jenis_keluar)) OR ($o_data_feeder->id_jenis_keluar == 0)) {
                    // $mba_score_student = $this->Scm->get_sum_merit_credit(array(
                    //     'sc.student_id' => $a_data['id_registrasi_mahasiswa'],
                    //     'sc.academic_year_id' => $a_data['academic_year_id'],
                    //     // 'sc.semester_type_id' => $a_data['semester_type_id'],
                    //     'sc.score_approval' => 'approved',
                    //     'sc.score_display' => 'TRUE',
                    //     'curs.curriculum_subject_type != ' => 'extracurricular'
                    // ), $mba_semester_type_in);
                    $mba_score_data = $this->Scm->get_score_data([
                        'sc.student_id' => $a_data['id_registrasi_mahasiswa'],
                        'sc.academic_year_id' => $a_data['academic_year_id'],
                        'sc.score_approval' => 'approved',
                        'sc.semester_id !=' => '17',
                        'curriculum_subject_credit !=' => '0',
                        'curriculum_subject_type !=' => 'extracurricular'
                    ], $mba_semester_type_in);

                    $i_sks_portal = 0;
                    $i_merit_portal = 0;
                    if ($mba_score_data) {
                        foreach ($mba_score_data as $o_score) {
                            $i_sks_portal += $o_score->curriculum_subject_credit;
                            $i_grade_point = $this->grades->get_grade_point($o_score->score_sum);
                            $i_merit_portal += $this->grades->get_merit($o_score->curriculum_subject_credit, $i_grade_point);
                        }
                    }
                    $s_ips_portal = $this->grades->get_ipk($i_merit_portal, $i_sks_portal);

                    $i_biaya_kuliah = $this->_get_biaya_masuk($o_student, $o_student->personal_data_id);
                    // $i_biaya_kuliah = $this->get_biaya_semester($mbo_student_semester_data->batch, $mbo_student_semester_data->faculty_id, $i_sks_portal);
                    $a_feeder_data = array(
                        'biaya_kuliah_smt' => "$i_biaya_kuliah",
                        'id_pembiayaan' => '1',
                        'ipk' => $this->_get_ipk_student($o_student->student_id),
                        'ips' => $s_ips_portal
                    );
                    $mba_student_from_abroad_data = $this->Iom->get_international_data(['ex.student_id' => $a_data['id_registrasi_mahasiswa'], 'ex.academic_year_id' => $a_data['academic_year_id']]);
                    if ($mba_student_from_abroad_data) {
                        // if ($mba_student_from_abroad_data[0]->program_id == '7') {
                            $a_feeder_data['id_status_mahasiswa'] = 'M';
                        // }
                    }
                    else {
                        switch ($mbo_student_semester_data->student_semester_status) {
                            case 'active':
                                // print('<pre>');
                                // print_r($mba_score_student);exit;
                                
                                $a_feeder_data['id_status_mahasiswa'] = 'A';
                                // $a_feeder_data['ipk'] = $mbo_student_semester_data->student_semester_gpa;
                                $a_feeder_data['sks_semester'] = (!is_null($i_sks_portal)) ? $i_sks_portal : 0;
                                // if ($s_id_registrasi_mahasiswa == 'da05c1b4-b3ca-47d1-8be9-6ae5b437cc27') {
                                //     $a_feeder_data['sks_semester'] = 24;
                                // }
                                // $a_feeder_data['total_sks'] = 0;
                                // $a_feeder_data['ips'] = 0;
                                // $a_feeder_data['ipk'] = 0;
                                break;

                            case 'graduated':
                                // print('<pre>');
                                // print_r($mba_score_student);exit;
                                
                                $a_feeder_data['id_status_mahasiswa'] = 'A';
                                // $a_feeder_data['ipk'] = $mbo_student_semester_data->student_semester_gpa;
                                $a_feeder_data['sks_semester'] = (!is_null($i_sks_portal)) ? $i_sks_portal : 0;
                                // if ($s_id_registrasi_mahasiswa == 'da05c1b4-b3ca-47d1-8be9-6ae5b437cc27') {
                                //     $a_feeder_data['sks_semester'] = 24;
                                // }
                                // $a_feeder_data['total_sks'] = 0;
                                // $a_feeder_data['ips'] = 0;
                                // $a_feeder_data['ipk'] = 0;
                                break;
                            
                            case 'inactive':
                                $a_feeder_data['id_status_mahasiswa'] = 'N';
                                break;
            
                            case 'onleave':
                                $a_feeder_data['id_status_mahasiswa'] = 'C';
                                break;
            
                            default:
                                break;
                        }
                    }

                    // if ($a_data['id_mahasiswa'] == '86aa1d4c-f4c9-423a-b16f-5f02890c0839') {
                        // print('<pre>');var_dump($a_feeder_data);exit;
                    // }

                    $a_feeder_list_perkuliahan_data = $this->feederapi->post('GetListPerkuliahanMahasiswa', array(
                        'filter' => "id_registrasi_mahasiswa = '$s_id_registrasi_mahasiswa' AND id_semester='$s_semester_dikti'"
                    ));
                    // print('<pre>');var_dump($a_feeder_data);exit;

                    if ($a_feeder_list_perkuliahan_data->error_code != '0') {
                        print('Error get data from feeder: ');
                        var_dump($a_feeder_list_perkuliahan_data);exit;
                    }
                    else if (count($a_feeder_list_perkuliahan_data->data) == 0) {
                        // 
                        // if (($s_id_registrasi_mahasiswa == '1d34a31e-60db-43e4-874c-7d5321e18d73') AND ($s_semester_dikti == '20201')) {
                        //     $a_feeder_data['sks_semester'] += 3;
                        // }else if (($s_id_registrasi_mahasiswa == 'b9720536-77cf-4321-bde9-4c866f88d3cb') AND ($s_semester_dikti == '20201')) {
                        //     # code...
                        // }
                        // print('insert<br>');
                        $a_feeder_data['id_registrasi_mahasiswa'] = $s_id_registrasi_mahasiswa;
                        $a_feeder_data['id_semester'] = $s_semester_dikti;
                        $a_result = $this->feederapi->post('InsertPerkuliahanMahasiswa', array(
                            'record' => $a_feeder_data
                        ));
                    }else{
                        // print('update<br>');
                        $a_result = $this->feederapi->post('UpdatePerkuliahanMahasiswa', array(
                            'key' => array(
                                'id_registrasi_mahasiswa' => $s_id_registrasi_mahasiswa,
                                'id_semester' => $s_semester_dikti
                            ),
                            'record' => $a_feeder_data
                        ));
                    }

                    // print('<pre>');var_dump($a_result);exit;
                    if ($a_result->error_code == 0) {
                        $a_return = array('code' => 0);
                    }else{
                        $a_return = array('code' => 3, 'message' => $a_result->error_desc, 'data' => $a_feeder_data, 'student' => $o_student->personal_data_name);
                    }
                }else{
                    // $a_return = array('code' => 1, 'message' => $o_data_feeder);
                    $a_return = array('code' => 1, 'message' => 'Mahasiswa tidak aktif');
                }
            }else{
                $a_return = array('code' => 1, 'message' => $a_feeder_list_riwayat_pendidikan->error_desc, 'data' => $a_feeder_list_riwayat_pendidikan);
            }
        }else{
            $a_return = array('code' => 0);
        }

        return $a_return;
        // print('<pre>');var_dump($a_return);
        // exit;
    }

    function _get_ipk_student($s_student_id) {
        $a_result = $this->feederapi->post('GetDetailPerkuliahanMahasiswa', [
			'filter' => "id_registrasi_mahasiswa='$s_student_id'",
			'order' => "id_semester ASC"
		]);

        $ipk = 0;
        if ($a_result->error_code == 0) {
            $i_total_sks_portal = 0;
			$i_total_merit_portal = 0;

            foreach ($a_result->data as $o_semester) {
                $s_semester_type_id = substr($o_semester->id_semester, 4);
                $s_academic_year_id = substr($o_semester->id_semester, 0, 4);

                $a_semester_type = ($s_semester_type_id == 3) ? [3, 7, 8] : [$s_semester_type_id];
				$mba_score_data = $this->Scm->get_score_data([
					'sc.student_id' => $s_student_id,
					'sc.academic_year_id' => $s_academic_year_id,
					'sc.score_approval' => 'approved',
					// 'sc.score_display' => 'TRUE',
					'sc.semester_id !=' => '17',
					'curriculum_subject_credit !=' => '0',
					'curriculum_subject_type !=' => 'extracurricular'
				], $a_semester_type);

                $i_sks_portal = 0;
				$i_merit_portal = 0;
				if ($mba_score_data) {
					foreach ($mba_score_data as $o_score) {
						$i_sks_portal += $o_score->curriculum_subject_credit;
						$i_grade_point = $this->grades->get_grade_point($o_score->score_sum);
						$i_merit_portal += $this->grades->get_merit($o_score->curriculum_subject_credit, $i_grade_point);
					}
				}

                // $s_ips_portal = $this->grades->get_ipk($i_merit_portal, $i_sks_portal);
                $i_total_sks_portal += $i_sks_portal;
				$i_total_merit_portal += $i_merit_portal;
                
                // $s_current_ipk_portal = $this->grades->get_ipk($i_total_merit_portal, $i_total_sks_portal);
            }

            $ipk = $this->grades->get_ipk($i_total_merit_portal, $i_total_sks_portal);
        }

        return $ipk;
    }

    public function check_graduate_year()
    {
        // $s_id_per = '20212';
        
        // print($s_year);exit;
        
        // $a_feeder_data = $this->feederapi->post('GetListMahasiswaLulusDO', array(
        //     'filter' => "id_registrasi_mahasiswa='75abb531-8301-4c74-9d4e-5e969d2a20bb'"
        // ));
        // print('<pre>');var_dump($a_feeder_data);exit;
        $a_graduate_year_id = [2019, 2020, 2021, 2022];
        print('<table border="1">');
        print('<tr>');
        print('<td>Nama Mahasiswa</td>');   
        print('<td>Prodi</td>');
        print('<td>Batch</td>');
        print('<td>Periode Lulus Akademik</td>');
        print('<td>Periode Lulus Forlap</td>');
        print('<td>Periode Lulus Revisi</td>');
        print('<td>Tanggal SK Yudisium</td>');
        print('<td>No SK Yudisium</td>');
        print('<td>No Seri Ijazah</td>');
        print('<td>IPK</td>');
        print('<td>Tanggal Masuk</td>');
        print('<td>Tanggal Keluar</td>');
        print('<td>Lama Perkuliahan</td>');
        print('<td>Jenis Keluar</td>');
        print('</tr>');

        foreach ($a_graduate_year_id as $s_graduate_year_id) {
            $mba_list_student = $this->Stm->get_student_filtered([
                'ds.graduated_year_id' => $s_graduate_year_id
            ]);
            if ($mba_list_student) {
                foreach ($mba_list_student as $o_student) {
                    print('<tr>');
                    print('<td>="'.$o_student->personal_data_name.'"</td>');
                    print('<td>="'.$o_student->study_program_abbreviation.'"</td>');
                    print('<td>="'.$o_student->academic_year_id.'"</td>');
                    print('<td>="'.$o_student->graduated_year_id.'"</td>');
                    $a_feeder_data = $this->feederapi->post('GetListMahasiswaLulusDO', array(
                        'filter' => "id_registrasi_mahasiswa='".$o_student->student_id."' AND id_jenis_keluar='1'"
                    ));
                    $s_periode_lulus_rev = (intval($o_student->graduated_year_id) - 1).'2';
                    
                    if (count($a_feeder_data->data) > 0) {
                        $registrasi_data = $a_feeder_data->data[0];
                        $s_year_graduate_forlap = substr($registrasi_data->id_periode_keluar, 0, 4);

                        
                        $s_periode_lulus_rev = ($s_periode_lulus_rev == $registrasi_data->id_periode_keluar) ? '' : $s_periode_lulus_rev;
                        $s_ipk = number_format(doubleval($registrasi_data->ipk), 2);
                        $tgl1 = new DateTime($registrasi_data->tgl_masuk_sp);
                        $tgl2 = new DateTime($registrasi_data->tgl_keluar);
                        $jarak = $tgl2->diff($tgl1);

                        $s_periodekeluar = (empty($registrasi_data->id_periode_keluar)) ? '' : '="'.$registrasi_data->id_periode_keluar.'"';
                        $s_periode_lulus_rev = (empty($s_periode_lulus_rev)) ? '' : '="'.$s_periode_lulus_rev.'"';
                        $s_tglskyudisium = (empty($registrasi_data->tgl_sk_yudisium)) ? '' : '="'.$registrasi_data->tgl_sk_yudisium.'"';
                        $s_skyudisium = (empty($registrasi_data->sk_yudisium)) ? '' : '="'.$registrasi_data->sk_yudisium.'"';
                        $seriijazah = (empty($registrasi_data->no_seri_ijazah)) ? '' : '="'.$registrasi_data->no_seri_ijazah.'"';
                        $s_ipk = (empty($s_ipk)) ? '' : '="'.$s_ipk.'"';
                        $tgl_masuk_sp = (empty($registrasi_data->tgl_masuk_sp)) ? '' : '="'.$registrasi_data->tgl_masuk_sp.'"';
                        $stgl_keluar = (empty($registrasi_data->tgl_keluar)) ? '' : '="'.$registrasi_data->tgl_keluar.'"';
                        $s_periodekeluar = (empty($registrasi_data->id_periode_keluar)) ? '' : '="'.$registrasi_data->id_periode_keluar.'"';

                        print('<td>'.$s_periodekeluar.'</td>');
                        print('<td>'.$s_periode_lulus_rev.'</td>');
                        print('<td>'.$s_tglskyudisium.'</td>');
                        print('<td>'.$s_skyudisium.'</td>');
                        print('<td>'.$seriijazah.'</td>');
                        print('<td>'.$s_ipk.'</td>');
                        print('<td>'.$tgl_masuk_sp.'</td>');
                        print('<td>'.$stgl_keluar.'</td>');
                        print('<td>="'.$jarak->y.' tahun, '.$jarak->m.' bulan, '.$jarak->d.' hari"</td>');
                        print('<td>'.$registrasi_data->id_jenis_keluar.'</td>');
                    }
                    print('</tr>');
                }
            }
        }
        print('</table>');
    }

    public function post_insert_riwayat_pendidikan($s_student_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        // $mbo_student_data = false;
        if ($mbo_student_data) {
            if ($mbo_student_data->student_status == 'active') {
                // print('<pre>');
                $mba_prodi_data = $this->General->get_where('ref_study_program', array('study_program_id' => $mbo_student_data->study_program_id))[0];
                $mbo_student_start_semester = $this->Smm->get_student_start_semester($s_student_id);
                // var_dump($mbo_student_start_semester);exit;
                if (!$mbo_student_start_semester) {
                    $this->print_result('error', 'Student semester data: '.$mbo_student_data->personal_data_name.' ('.$s_student_id.')');
                    exit;
                }else{
                    $a_student_reg_data = array(
                        'id_mahasiswa' => $mbo_student_data->personal_data_id,
                        'nim' => $mbo_student_data->student_number,
                        'id_jenis_daftar' => ($mbo_student_data->student_type == 'regular') ? '1' : '2',
                        // 'id_jenis_daftar' => '1',
                        'id_jalur_daftar' => 12,
                        'id_periode_masuk' => $mbo_student_start_semester->academic_year_id.$mbo_student_start_semester->semester_type_id,
                        // 'tanggal_daftar' => (!is_null($mbo_student_data->student_date_enrollment)) ? date('Y-m-d', strtotime($mbo_student_data->student_date_enrollment)) : date('Y-m-d', strtotime($mbo_student_data->date_added)),
                        'tanggal_daftar' => date('Y-m-d', strtotime('2023-09-04')),
                        'id_perguruan_tinggi' => $this->id_pt,
                        'id_prodi' => (!is_null($mba_prodi_data->study_program_main_id)) ? $mba_prodi_data->study_program_main_id : $mbo_student_data->study_program_id,
                        'biaya_masuk' => '0',
                        'id_pembiayaan' => '1'
                    );

                    $a_mahasiswa_feeder = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
                        'filter' => "id_registrasi_mahasiswa='$s_student_id'"
                    ));

                    if ($mbo_student_data->student_type == 'transfer') {
                        $mbo_from_student = $this->General->get_where('dt_student', ['personal_data_id' => $mbo_student_data->personal_data_id, 'student_status != ' => 'active']);
                        $mbo_from_student = $mbo_from_student[0];

                        $a_student_reg_data['id_perguruan_tinggi_asal'] = $this->id_pt;
                        $a_student_reg_data['id_prodi_asal'] = $mbo_from_student->study_program_id;
                        $a_student_reg_data['sks_diakui'] = '11';
                    }

                    if (count($a_mahasiswa_feeder->data) == 0) {
                        # insert
                        // print('insert');
                        $a_result = $this->feederapi->post('InsertRiwayatPendidikanMahasiswa', array(
                            'record' => $a_student_reg_data
                        ));
                        if ($a_result->error_code == 0) {
                            $result_data = $a_result->data;
                            $s_id_registrasi = $result_data->id_registrasi_mahasiswa;
                            $a_return = array('code' => 0, 'id_registrasi_mahasiswa' => $s_id_registrasi);
                        }else{
                            $s_id_registrasi = false;
                            $a_return = array('code' => 1, 'id_registrasi_mahasiswa' => false, 'message' => $a_result->error_desc);
                        }
                    }else{
                        # update
                        // print('update');
                        $registrasi_data = $a_mahasiswa_feeder->data;
                        $s_id_registrasi = $registrasi_data[0]->id_registrasi_mahasiswa;
                        $a_result = $this->feederapi->post('UpdateRiwayatPendidikanMahasiswa', array(
                            'key' => array(
                                'id_registrasi_mahasiswa' => $s_id_registrasi
                            ),
                            'record' => $a_student_reg_data
                        ));

                        if ($a_result->error_code == 0) {
                            $a_return = array('code' => 0, 'id_registrasi_mahasiswa' => $s_id_registrasi);
                        }else{
                            $a_return = array('code' => 1, 'id_registrasi_mahasiswa' => $s_id_registrasi, 'message' => $a_result->error_desc);
                        }
                    }

                    // print('<pre>');
                    // var_dump($a_result);

                    if ($s_id_registrasi) {
                        # update student_id
                        $save_record = $this->Stm->update_student_data(array('student_id' => $s_id_registrasi), $mbo_student_data->student_id);
                    }
                }
            }else{
                $a_return = array('code' => 0, 'id_registrasi_mahasiswa' => false, 'message' => 'student_id '.$s_student_id.' '.$mbo_student_data->student_status);
            }
        }else{
            $a_return = array('code' => 1, 'id_registrasi_mahasiswa' => false, 'message' => 'not found! student_id '.$s_student_id);
        }

        // return $a_return;
        var_dump($a_return);
    }

    public function checking_number($s_number)
    {
        $res = preg_replace("/[0-9]/", "", $s_number);
        if ($res == '') {
            if ($s_number == '0') {
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    public function sync_student_data($s_student_id)
    {
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.student_id' => $s_student_id
        ], ['active', 'inactive', 'onleave']);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            print('<h3>'.$o_student->personal_data_name.'</h3>');

            $a_mahasiswa_feeder = $this->feederapi->post('GetListMahasiswa', array(
                // 'filter' => "nama_mahasiswa = '$student->personal_data_name' AND tempat_lahir = '$student->personal_data_place_of_birth' AND tanggal_lahir = '$student->personal_data_date_of_birth' AND nama_ibu_kandung = '$student->personal_data_mother_maiden_name'"
                'filter' => "id_mahasiswa = '$o_student->personal_data_id'"
            ));
            if ($a_mahasiswa_feeder->error_code != '0') {
                $a_result = array('code' => 1, 'message' => 'Error sinkronisasi: '.$a_mahasiswa_feeder->error_desc, 'process' => $a_mahasiswa_feeder);
                print('<pre>');var_dump($a_result);exit;
            }else if (count($a_mahasiswa_feeder->data) == 0) {
                $post_biodata = $this->post_insert_biodata($o_student->student_id);
            }
            else {
                $post_biodata = $this->post_insert_biodata($o_student->student_id, $o_student->personal_data_id);
            }

            if ($post_biodata['code'] == 0) {
                print('<p>Sinkronisasi biodata mahasiswa '.$o_student->personal_data_name.' berhasil</p>');
                $post_riwayat = $this->post_insert_riwayat_pendidikan($s_student_id);
                if ($post_riwayat['code'] > 0) {
                    print('<p>Sinkronisasi riwayat pendidikan '.$student->personal_data_name.' gagal: id_mahasiswa ('.$s_id_mahasiswa.'): "'.$post_riwayat['message'].'"</p><br>');
                }else{
                    print('<p>Sinkronisasi riwayat pendidikan mahasiswa berhasil: Data '.$o_student->personal_data_name.'</p><br><br>');
                }
            }else{
                print('<p>Sinkronisasi biodata mahasiswa '.$o_student->personal_data_name.' error : id_mahasiswa ('.$o_student->personal_data_id.') -'.$post_biodata['message'].'</p><br>');
            }
        }
        else {
            print('student not found!');exit;
        }
    }

    public function post_insert_biodata($s_student_id, $s_id_mahasiswa = false)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $mbo_student_family = $this->Fmm->get_family_by_personal_data_id($mbo_student_data->personal_data_id);
            $mbo_student_country = $this->General->get_where('ref_country', array('country_id' => $mbo_student_data->citizenship_id))[0];
            $country_id = ($mbo_student_country) ? $mbo_student_country->country_code : 'ID';
            if (!is_null($mbo_student_data->religion_id)) {
                $mbo_religion_data = $this->General->get_where('ref_religion', array('religion_id' => $mbo_student_data->religion_id))[0];
                $religion_id = ($mbo_religion_data) ? $mbo_religion_data->religion_feeder_id : 99;
            }else{
                $religion_id = 99;
            }
            $mba_student_family_father = false;
            $mba_student_family_guardian = false;
            if ($mbo_student_family) {
                $mba_student_family_father = $this->Fmm->get_family_members($mbo_student_family->family_id, array('family_member_status' => 'father'));
                $mba_student_family_guardian = $this->Fmm->get_family_members($mbo_student_family->family_id, array('family_member_status' => 'guardian'));
            }
            $student_address = $this->Pdm->get_personal_address($mbo_student_data->personal_data_id)[0];
            if ($student_address) {
                $address_rt = (strlen($student_address->address_rt == 3)) ? substr($student_address->address_rt, 1, 2) : $student_address->address_rt;
                $address_rw = (strlen($student_address->address_rw == 3)) ? substr($student_address->address_rw, 1, 2) : $student_address->address_rw;
            }else{
                $address_rt = NULL;
                $address_rw = NULL;
            }

            $b_sync = true;
            if ($mbo_student_data->personal_data_nationality == 'WNI') {
                if (strlen($mbo_student_data->personal_data_id_card_number) != 16) {
                    $b_sync = false;
                    $a_return = array('code' => 1, 'message' => 'Panjang NIK tidak sama dengan 16 digit');
                }
            }else if (is_null($mbo_student_data->personal_data_id_card_number)) {
                $b_sync = false;
                $a_return = array('code' => 1, 'message' => 'No paspor kosong');
            }
            if ($b_sync) {
                $a_student_feeder_data = array(
                    'jenis_kelamin' => ($mbo_student_data->personal_data_gender == 'M') ? 'L' : (($mbo_student_data->personal_data_gender == 'F') ? 'P' : '*'),
                    'jalan' => (($student_address) AND (strlen($student_address->address_street) <= 80)) ? $student_address->address_street : NULL,
                    'rt' => ($this->checking_number($address_rt)) ? $address_rt : NULL,
                    'rw' => ($this->checking_number($address_rw)) ? $address_rw : NULL,
                    'kelurahan' => ($student_address) ? $student_address->address_sub_district : NULL,
                    'kode_pos' => ($student_address) ? $student_address->address_zipcode : NULL,
                    'nik' => $mbo_student_data->personal_data_id_card_number,
                    'nisn' => $mbo_student_data->student_nisn,
                    'nama_ayah' => ($mba_student_family_father) ? $mba_student_family_father[0]->personal_data_name : '',
                    'id_kebutuhan_khusus_ayah' => 0,
                    'id_kebutuhan_khusus_ibu' => 0,
                    'nama_wali' => ($mba_student_family_guardian) ? $mba_student_family_guardian[0]->personal_data_name : '',
                    'id_kebutuhan_khusus_mahasiswa' => 0,
                    // 'telepon' => $mbo_student_data->personal_data_phone,
                    'handphone' => ($this->checking_number($mbo_student_data->personal_data_cellular)) ? $mbo_student_data->personal_data_cellular : NULL,
                    'email' => $mbo_student_data->personal_data_email,
                    'penerima_kps' => 0,
                    'id_wilayah' => ($student_address) ? $student_address->dikti_wilayah_id : NULL,
                    'id_agama' => $religion_id,
                    'kewarganegaraan' => $country_id
                );

                // if ($s_student_id == '') {
                //     $a_student_feeder_data['kelurahan'] = 'lower saxony';
                // }
                
                if ($s_id_mahasiswa) {
                    $a_result = $this->feederapi->post('UpdateBiodataMahasiswa', array(
                        'key' => array(
                            'id_mahasiswa' => $s_id_mahasiswa
                        ),
                        'record' => $a_student_feeder_data
                    ));
    
                    if ($a_result->error_code == 0) {
                        $a_return = array('code' => 0);
                    }else{
                        $a_return = array('code' => 1, 'message' => $a_result->error_desc);
                    }
    
                    $a_return['id_mahasiswa'] = $s_id_mahasiswa;
                }else{
                    $a_student_feeder_data['nama_mahasiswa'] = $mbo_student_data->personal_data_name;
                    $a_student_feeder_data['tempat_lahir'] = $mbo_student_data->personal_data_place_of_birth;
                    $a_student_feeder_data['tanggal_lahir'] = $mbo_student_data->personal_data_date_of_birth;
                    $a_student_feeder_data['nama_ibu_kandung'] = $mbo_student_data->personal_data_mother_maiden_name;
    
                    $a_result = $this->feederapi->post('InsertBiodataMahasiswa', array(
                        'record' => $a_student_feeder_data
                    ));
    
                    if ($a_result->error_code == 0) {
                        $a_data = $a_result->data;
                        $a_student_personal_update = array(
                            'personal_data_id' => $a_data->id_mahasiswa
                        );
    
                        // $save_record = $this->Stm->update_student_data($a_student_update, $mbo_student_data->student_id);
                        $save_record = $this->Pdm->update_personal_data($a_student_personal_update, $mbo_student_data->personal_data_id);
                        if ($save_record) {
                            $a_return = array('code' => 0);
                        }else{
                            $a_return = array('code' => 1, 'message' => $a_student_personal_update);
                        }
    
                        $a_return['id_mahasiswa'] = $a_data->id_mahasiswa;
                    }else{
                        $a_return = array('code' => 1, 'message' => $a_result->error_desc, 'id_mahasiswa' => $mbo_student_data->personal_data_id);
                        print('<pre>');var_dump($a_result);exit;
                    }
                }
            }

            // print('<pre>');
            // var_dump($a_result);exit;
        }else{
            $a_return = array('code' => 1, 'message' => 'not found! student_id '.$s_student_id);
        }

        // return $a_return;
        var_dump($a_return);
    }
}
