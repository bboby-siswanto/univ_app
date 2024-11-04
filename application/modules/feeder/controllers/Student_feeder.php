<?php
class Student_feeder extends App_core
{
    private $id_pt = '7ed83401-2862-4e12-aa60-79defe7b90a8';
    public function __construct()
	{
		parent::__construct('academic');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('academic/Class_group_model', 'Cgm');
		$this->load->model('academic/Curriculum_model', 'Cm');
		$this->load->model('academic/Semester_model', 'Smm');
		$this->load->model('academic/Subject_model', 'Sbj');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('feeder/Feeder_model', 'FeM');
		$this->load->model('employee/Employee_model', 'EeM');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('personal_data/Family_model', 'Fmm');
        $this->load->model('finance/Invoice_model', 'Inv');
		$this->load->library('FeederAPI', ['mode' => 'production']);
		// $this->load->library('FeederAPI', ['mode' => 'sandbox']);
		
		// var_dump($this->session->userdata('environment'));
	}

    public function get_student_data($s_id_mahasiswa)
    {
        $a_mahasiswa_feeder = $this->feederapi->post('GetDataLengkapMahasiswaProdi', [
			'filter' => "id_mahasiswa = '$s_id_mahasiswa'"
		]);
        print('<pre>');var_dump($a_mahasiswa_feeder);exit;
    }

    public function sync_student_biodata($s_student_id = false)
    {
        // $a_status_allowed = ['active', 'inactive', 'onleave', 'graduated'];
        $a_status_allowed = ['active'];
        $a_error_list = [];
        if ($s_student_id) {
            $o_student = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id])[0];
            if ($o_student) {
                $a_sync_biodata = $this->sync_student_data($o_student);
                print('<pre>');var_dump($a_sync_biodata);exit;
            }
        }else{
            $s_academic_year_id = 2023;
            $mba_student_data = $this->Stm->get_student_filtered(['ds.academic_year_id' => $s_academic_year_id], $a_status_allowed);
            print('<pre>');
            // print(count($mba_student_data));exit;
            if ($mba_student_data) {
                foreach ($mba_student_data as $o_student) {
                    // print('<pre>');var_dump($o_student);exit;
                    // if ($o_student->personal_data_nationality != 'WNA') {
                        if ($o_student->personal_data_id != '9d9765d0-b2e1-499a-86f3-5f6f08c59e43') {
                            $a_sync_biodata = $this->sync_student_data($o_student);
                            
                            if ($a_sync_biodata['code'] > 0) {
                                array_push($a_error_list, $a_sync_biodata['message']);
                            }
                            print('<pre>');var_dump($a_sync_biodata);
                            // var_dump($a_sync_biodata['code']);
                        }
                    // }
                }
            }
        }

        if (count($a_error_list) > 0) {
            print('<li>'.implode('</li><li>', $a_error_list).'</li>');
            // var_dump($a_error_list);
        }else{
            print('no error!');
        }
    }

    public function sync_student_data($o_student)
    {
        $a_mahasiswa_feeder = $this->feederapi->post('GetListMahasiswa', [
			'filter' => "id_mahasiswa = '$o_student->personal_data_id'"
		]);

        if ($a_mahasiswa_feeder->error_code != '0') {
            $a_result = array('code' => 1, 'message' => 'Gagal mendapatkan data mahasiswa: '.$a_mahasiswa_feeder->error_desc, 'process' => $a_mahasiswa_feeder);
        }
        else if (count($a_mahasiswa_feeder->data) == 0) {
            $a_result = $this->sinkronisasi_biodata($o_student);
        }
        else if (count($a_mahasiswa_feeder->data) == 1) {
            $data = $a_mahasiswa_feeder->data;
            $a_result = $this->sinkronisasi_biodata($o_student, $data[0]->id_mahasiswa);
        }
        else{
            $a_result = array('code' => 1, 'message' => 'Jumlah Data mahasiswa tidak sesuai: '.$a_mahasiswa_feeder->error_desc, 'process' => $a_mahasiswa_feeder);
        }

        if ($a_result['code'] == 0) {
            $s_id_mahasiswa = $a_result['id_mahasiswa'];
            $o_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_id_mahasiswa])[0];
            if (($o_personal_data) AND ($o_personal_data->personal_data_email == $o_student->personal_data_email)) {
                $a_result = $this->sinkronisasi_riwayat_pendidikan($o_student, $s_id_mahasiswa);

                if ($a_result['code'] == 0) {
                    // $this_transfer_prodi = $this->this_transfer_prodi($s_id_mahasiswa);
                    // if (($o_student->student_type == 'transfer') OR ($this_transfer_prodi)) {
                    //     $a_result = $this->sinkronisasi_nilai_transfer($o_student, $a_result['id_registrasi_mahasiswa']);
                    // }
                }
            }else{
                $a_result = array('code' => 1, 'message' => $o_student->personal_data_name.': Gagal mengupdate data mahasiswa disistem!');
            }
        }
        
        return $a_result;

        // print('<pre>');
        // var_dump($a_result);
    }

    public function sinkronisasi_nilai_transfer($o_student, $s_id_registrasi)
    {
        return array('code' => 3, 'message' => $o_student->personal_data_name.': tidak dapat melakukan sinkronisasi nilai transfer untuk saat ini!');
        // $mba_student_transfer_credit = $this->Scm->get_score_data(array(
        //     'sc.student_id' => $s_id_registrasi,
        //     'sc.semester_type_id' => '5',
        //     'curs.curriculum_subject_type != ' => 'extracurricular',
        //     'sc.score_grade != ' => 'F',
        // ));

        // if ($mba_student_transfer_credit) {
        //     foreach ($mba_student_transfer_credit as $o_score) {
        //         # code...
        //     }
        // }else{
        //     $a_return = ['code' => 1, 'message' => 'nilai transfer tidak ditemukan diportal!'];
        // }
    }

    function sync_riwayat_didik($s_student_id) {
        $o_student = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
        if ($o_student) {
            $o_student = $o_student[0];
            $sinkron = $this->sinkronisasi_riwayat_pendidikan($o_student, $o_student->personal_data_id);
            print('<pre>');var_dump($sinkron);exit;
        }
        else {
            print('Student not found!');exit;
        }
    }

    public function sinkronisasi_riwayat_pendidikan($o_student, $s_id_mahasiswa)
    {
        $mbo_student_start_semester = $this->Smm->get_student_start_semester($o_student->student_id);
        if (!$mbo_student_start_semester) {
            $a_return = ['code' => 2, 'message' => $o_student->personal_data_name.': first semester not found!'];
        }else{
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

            $mbo_student_has_scholarship = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $s_id_mahasiswa]);

            $a_student_reg_data = array(
                'id_mahasiswa' => $s_id_mahasiswa,
                'nim' => $o_student->student_number,
                'id_jenis_daftar' => ($o_student->student_type == 'regular') ? '1' : '2',
                'id_periode_masuk' => $mbo_student_start_semester->academic_year_id.$mbo_student_start_semester->semester_type_id,
                // 'tanggal_daftar' => (!is_null($o_student->student_date_enrollment)) ? date('Y-m-d', strtotime($o_student->student_date_enrollment)) : date('Y-m-d', strtotime($o_student->date_added)),
                'tanggal_daftar' => '2023-03-06',
                'id_perguruan_tinggi' => $this->id_pt,
                'id_prodi' => (!is_null($o_student->study_program_main_id)) ? $o_student->study_program_main_id : $o_student->study_program_id,
                'id_jalur_daftar' => '12',
                // 'id_perguruan_tinggi_asal',
                // 'id_prodi_asal',
                'biaya_masuk' => '11000000',
                'id_pembiayaan' => ($mbo_student_has_scholarship) ? '2' : '1'
            );

            if ($o_student->student_type != 'regular') {
                $mba_scorecredit_data = $this->Scm->get_score_semester([
                    'sc.student_id' => $o_student->student_id,
                    'sc.semester_id' => 18
                ]);
                $d_sum_credit = 0;
                if ($mba_scorecredit_data) {
                    foreach ($mba_scorecredit_data as $o_score) {
                        $d_sum_credit += $o_score->curriculum_subject_credit;
                    }
                }

                $mba_student_academic_history = $o_student->institution_id;
                $a_student_reg_data['sks_diakui'] = $d_sum_credit;
                // $a_student_reg_data['id_perguruan_tinggi_asal'] = $o_student->institution_id;
                // $a_student_reg_data['id_prodi_asal'] = '';
            }
            
            $a_mahasiswa_feeder = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
                'filter' => "id_registrasi_mahasiswa='$o_student->student_id'"
            ));

            if (count($a_mahasiswa_feeder->data) == 0) {
                # insert
                $a_result = $this->feederapi->post('InsertRiwayatPendidikanMahasiswa', array(
                    'record' => $a_student_reg_data
                ));

                if ($a_result->error_code == 0) {
                    $result_data = $a_result->data;
                    $s_id_registrasi = $result_data->id_registrasi_mahasiswa;

                    if ($s_id_registrasi != $o_student->student_id) {
                        $this->Stm->update_student_data(['student_id' => $s_id_registrasi], $o_student->student_id);
                    }
                    $a_return = array('code' => 0, 'id_registrasi_mahasiswa' => $s_id_registrasi);

                }else{
                    $a_return = array('code' => 2, 'message' => $o_student->personal_data_name.': '.$a_result->error_desc, 'result_data' => $a_result);
                }

            }else{
                # update
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
                    $a_return = array('code' => 2, 'message' => $o_student->personal_data_name.': '.$a_result->error_desc, 'result_data' => $a_result);
                }
            }

            return $a_return;
        }
    }

    public function cek_update()
    {
        // $a_student_reg_data = array(
        //     'id_mahasiswa' => 'f730b6fc-ed8b-4da9-be70-2c8cde347301',
        //     'nim' => '11202204003',
        //     'id_jenis_daftar' => '2',
        //     'id_periode_masuk' => '20221',
        //     'tanggal_daftar' => '2022-09-05',
        //     'id_perguruan_tinggi' => '7ed83401-2862-4e12-aa60-79defe7b90a8',
        //     'id_prodi' => '903eb8ee-159e-406b-8f7e-38d63a961ea4',
        //     'sks_diakui' => '76',
        //     'id_perguruan_tinggi_asal' => 'edb5f561-e524-417f-b1ac-ca2392c059bd',
        //     'id_prodi_asal' => '',
        //     'biaya_masuk' => '29900000',
        //     'id_pembiayaan' => '1'
        // );
        $a_mahasiswa_feeder = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', array(
            'filter' => "id_mahasiswa='e524fa6a-c0d5-4cae-acd1-0d78250bd64d'"
        ));
        // $a_mahasiswa_feeder = $this->feederapi->post('UpdateRiwayatPendidikanMahasiswa', array(
        // $a_mahasiswa_feeder = $this->feederapi->post('InsertRiwayatPendidikanMahasiswa', array(
            // 'key' => array(
            //     'id_registrasi_mahasiswa' => 'a0c06620-0d22-4b06-bee3-14a15e57337c'
            // ),
        //     'record' => $a_student_reg_data
        // ));
        print('result<pre>');var_dump($a_mahasiswa_feeder);exit;
    }

    public function get_prodi_pt($s_id_pt)
    {
        $o_feeder_data = $this->feederapi->post('GetAllProdi', [
            'filter' => "id_perguruan_tinggi='$s_id_pt'"
        ]);
        print('<pre>');var_dump($o_feeder_data);exit;
    }

    public function sinkronisasi_biodata($o_student, $s_id_mahasiswa = false)
    {
        $b_sync = true;
        if ($o_student->personal_data_nationality == 'WNI') {
            if (strlen($o_student->personal_data_id_card_number) != 16) {
                $b_sync = false;
                $a_return = array('code' => 1, 'message' => $o_student->personal_data_name.': Panjang NIK tidak sama dengan 16 digit');
            }
        }else if (is_null($o_student->personal_data_id_card_number)) {
            $b_sync = false;
            $a_return = array('code' => 1, 'message' => $o_student->personal_data_name.': No paspor kosong');
        }
        
        if (!$b_sync) {
            return $a_return;
        }else{
            $o_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_student->personal_data_id])[0];

            $student_address = $this->Pdm->get_personal_address($o_student->personal_data_id)[0];
            if ($student_address) {
                $address_rt = (strlen($student_address->address_rt == 3)) ? substr($student_address->address_rt, 1, 2) : $student_address->address_rt;
                $address_rw = (strlen($student_address->address_rw == 3)) ? substr($student_address->address_rw, 1, 2) : $student_address->address_rw;
            }else{
                $address_rt = NULL;
                $address_rw = NULL;
            }

            $mbo_student_country = $this->General->get_where('ref_country', array('country_id' => $o_personal_data->citizenship_id))[0];
            $country_id = ($mbo_student_country) ? $mbo_student_country->country_code : 'ID';
            if (!is_null($o_personal_data->religion_id)) {
                $mbo_religion_data = $this->General->get_where('ref_religion', array('religion_id' => $o_personal_data->religion_id))[0];
                $religion_id = ($mbo_religion_data) ? $mbo_religion_data->religion_feeder_id : 99;
            }else{
                $religion_id = 99;
            }

            $mbo_student_family = $this->Fmm->get_family_by_personal_data_id($o_student->personal_data_id);
            $mba_student_family_father = false;
            $mba_student_family_guardian = false;
            if ($mbo_student_family) {
                $mba_student_family_father = $this->Fmm->get_family_members($mbo_student_family->family_id, array('family_member_status' => 'father'));
                $mba_student_family_guardian = $this->Fmm->get_family_members($mbo_student_family->family_id, array('family_member_status' => 'guardian'));
            }

            $nisn = $o_student->student_nisn;
            if (is_null($o_student->student_nisn)) {
                $nisn = '0';
            }
            $nisn = (strlen($nisn) <= 10) ? str_pad($nisn, 10, "0", STR_PAD_LEFT) : $nisn;
            $nisn = strtoupper($nisn);
            // print($nisn);exit;

            $a_biodata = array(
                'jenis_kelamin' => ($o_personal_data->personal_data_gender == 'M') ? 'L' : (($o_personal_data->personal_data_gender == 'F') ? 'P' : '*'),
                'jalan' => (($student_address) AND (strlen($student_address->address_street) <= 80)) ? $student_address->address_street : NULL,
                'rt' => ($this->checking_number($address_rt)) ? $address_rt : NULL,
                'rw' => ($this->checking_number($address_rw)) ? $address_rw : NULL,
                'kelurahan' => ($student_address) ? $student_address->address_sub_district : NULL,
                'kode_pos' => ($student_address) ? $student_address->address_zipcode : NULL,
                'nik' => $o_personal_data->personal_data_id_card_number,
                'nama_ayah' => ($mba_student_family_father) ? $mba_student_family_father[0]->personal_data_name : '',
                'id_kebutuhan_khusus_ayah' => 0,
                'id_kebutuhan_khusus_ibu' => 0,
                'nama_wali' => ($mba_student_family_guardian) ? $mba_student_family_guardian[0]->personal_data_name : '',
                'id_kebutuhan_khusus_mahasiswa' => 0,
                // 'telepon' => $o_personal_data->personal_data_phone,
                'handphone' => ($this->checking_number($o_personal_data->personal_data_cellular)) ? $o_personal_data->personal_data_cellular : NULL,
                'email' => $o_personal_data->personal_data_email,
                'penerima_kps' => 0,
                'id_wilayah' => ($student_address) ? $student_address->dikti_wilayah_id : NULL,
                'id_agama' => $religion_id,
                'kewarganegaraan' => $country_id,
                'nisn' => $nisn
            );
            
            if ($s_id_mahasiswa) {
                $a_result = $this->feederapi->post('UpdateBiodataMahasiswa', array(
                    'key' => array(
                        'id_mahasiswa' => $s_id_mahasiswa
                    ),
                    'record' => $a_biodata
                ));

                if ($a_result->error_code == 0) {
                    $a_return = array('code' => 0);
                }else{
                    $a_return = $a_result;
                    // $a_return = array('code' => 1, 'message' => $o_student->personal_data_name.': '.$a_result->error_desc);
                }

                $a_return['id_mahasiswa'] = $s_id_mahasiswa;
            }else{
                $a_biodata['nama_mahasiswa'] = $o_personal_data->personal_data_name;
                $a_biodata['tempat_lahir'] = $o_personal_data->personal_data_place_of_birth;
                $a_biodata['tanggal_lahir'] = $o_personal_data->personal_data_date_of_birth;
                $a_biodata['nama_ibu_kandung'] = $o_personal_data->personal_data_mother_maiden_name;

                $a_result = $this->feederapi->post('InsertBiodataMahasiswa', array(
                    'record' => $a_biodata
                ));

                if ($a_result->error_code == 0) {
                    $a_data = $a_result->data;
                    $a_student_personal_update = array(
                        'personal_data_id' => $a_data->id_mahasiswa
                    );

                    $save_record = $this->Pdm->update_personal_data($a_student_personal_update, $o_personal_data->personal_data_id);
                    if ($save_record) {
                        $a_return = array('code' => 0);
                    }else{
                        $a_return = array('code' => 1, 'message' => $a_student_personal_update);
                    }

                    $a_return['id_mahasiswa'] = $a_data->id_mahasiswa;
                }else{
                    print('<pre>');var_dump($a_result);exit;
                    // $a_return = $a_result;
                    // $a_return = (array) $a_return;
                    // $a_return['id_mahasiswa'] = $s_id_mahasiswa;
                    $a_return = array('code' => 1, 'message' => $o_student->personal_data_name.': '.$a_result->error_desc, 'id_mahasiswa' => $o_personal_data->personal_data_id);
                }
            }

            return $a_return;
        }
    }

    public function this_transfer_prodi($s_personal_data_id)
    {
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

    public function student_aktivitas_feeder_sync($s_batch)
    {
        print('warung tutup!');exit;
        // $o_feeder_student = $this->feederapi->post('GetAktivitasKuliahMahasiswa', [
        //     'filter' => "angkatan='$s_batch'"
        // ]);

        // if (count($o_feeder_student->data) > 0) {
        //     foreach ($o_feeder_student->data as $o_student_feeder) {
        //         if ($o_student_feeder->id_prodi != 'bfad84ea-f6f9-441e-af70-f75900b6112f') {
        //             $o_student = $this->General->get_where('dt_student', ['student_id' => $o_student_feeder->id_registrasi_mahasiswa])[0];
        //             if (($o_student) AND ((strtolower($o_student->student_number)) == strtolower($o_student_feeder->nim))) {
        //                 $s_academic_year_id = substr($o_student_feeder->id_semester, 0, 4);
        //                 $s_semester_type_id = substr($o_student_feeder->id_semester, 4, 1);

        //                 $mbo_student_semester = $this->General->get_where('dt_student_semester', [
        //                     'student_id' => $o_student->student_id,
        //                     'academic_year_id' => $s_academic_year_id,
        //                     'semester_type_id' => $s_semester_type_id
        //                 ])[0];

        //                 if (!$mbo_student_semester) {
        //                     // $s_status = 'inactive';
        //                     // switch ($o_student_feeder->id_status_mahasiswa) {
        //                     //     case 'A':
        //                     //         $s_status = 'active';
        //                     //         break;

        //                     //     case 'C':
        //                     //         $s_status = 'onleave';
        //                     //         break;

        //                     //     case 'N':
        //                     //         $s_status = 'inactive';
        //                     //         break;
                                
        //                     //     default:
        //                     //         $s_status = 'inactive';
        //                     //         break;
        //                     // }
        //                     print($o_student->student_id.' not found tahun akademik '.$o_student_feeder->id_semester.'<br>');
        //                     // $this->Smm->save_student_semester([
        //                     //     'student_id' => $o_student->student_id,
        //                     //     'academic_year_id' => $s_academic_year_id,
        //                     //     'semester_type_id' => $s_semester_type_id,
        //                     //     'student_semester_status' => $s_status
        //                     // ]);
        //                 }else{
        //                     $s_status = 'inactive';
        //                     switch ($o_student_feeder->id_status_mahasiswa) {
        //                         case 'A':
        //                             $s_status = 'active';
        //                             break;

        //                         case 'C':
        //                             $s_status = 'onleave';
        //                             break;

        //                         case 'N':
        //                             $s_status = 'inactive';
        //                             break;
                                
        //                         default:
        //                             $s_status = 'inactive';
        //                             break;
        //                     }

        //                     if ($mbo_student_semester->student_semester_status != $s_status) {
        //                         $this->Smm->save_student_semester([
        //                             'student_semester_status' => $s_status
        //                         ], [
        //                             'student_id' => $o_student->student_id,
        //                             'academic_year_id' => $s_academic_year_id,
        //                             'semester_type_id' => $s_semester_type_id,
        //                         ]);
        //                         print('u ');
        //                     }else{
        //                         print('. ');
        //                     }
        //                 }
        //             }else {
        //                 $a_aktivitas_dikti = $this->feederapi->post('GetListMahasiswa', [
        //                     'filter' => "id_registrasi_mahasiswa='$o_student_feeder->id_registrasi_mahasiswa'"
        //                 ]);

        //                 $a_aktivitas_dikti_data = $a_aktivitas_dikti->data[0];
        //                 if ($a_aktivitas_dikti_data->nama_status_mahasiswa != 'Mengundurkan diri') {
        //                     print($o_student_feeder->nama_mahasiswa);
        //                     print('<pre>');
        //                     var_dump($o_student);exit;
        //                     // print($o_student_feeder->nama_mahasiswa.'-'.$o_student->student_number.' tidak sama dengan '.$o_student_feeder->nim.' feeder!<br>');
        //                 }
        //             }
        //         }
        //     }
        // }

        // print('<pre>');
        // var_dump($o_feeder_student);
    }

    public function student_chemical()
    {
        print('warung tutup!');exit;
        // $o_student_kimia = $this->feederapi->post('GetListMahasiswa', [
        //     'filter' => "id_prodi='6ce5bc8b-10f5-456d-855d-aef18dc641f4'"
        // ]);

        // if (count($o_student_kimia->data) > 0) {
        //     foreach ($o_student_kimia->data as $o_dikti) {
        //         $mba_student_data = $this->General->get_where('dt_student', ['student_number' => $o_dikti->nim]);
        //         if (count($mba_student_data) > 1) {
        //             print('<pre>');
        //             var_dump($mba_student_data);exit;
        //         }else if (!$mba_student_data) {
        //             print('<pre>');
        //             var_dump($o_dikti);exit;
        //         }else if ($o_dikti->id_mahasiswa != $mba_student_data[0]->personal_data_id) {
        //             print('<pre>');
        //             var_dump($mba_student_data);exit;
        //         }else if ($o_dikti->id_registrasi_mahasiswa != $mba_student_data[0]->student_id) {
        //             $this->Stm->update_student_data(['student_id' => $o_dikti->id_registrasi_mahasiswa], $mba_student_data[0]->student_id);
        //             print($o_dikti->nama_mahasiswa.'<br>');
        //         }
        //     }
        // }
    }

    public function student_feeder_syncronize()
    {
        print('warung tutup!');exit;
        // $a_status_allowed = ['active', 'inactive', 'onleave', 'graduated'];
        // $mba_student_data = $this->Stm->get_student_filtered(false, $a_status_allowed);
        // if ($mba_student_data) {
        //     foreach ($mba_student_data as $o_student) {
        //         $o_feeder_student = $this->feederapi->post('GetListMahasiswa', [
        //             'filter' => "nim='$o_student->student_number' AND id_prodi != '6ce5bc8b-10f5-456d-855d-aef18dc641f4' AND nama_status_mahasiswa != 'Mengundurkan diri'"
        //         ]);

        //         if ($o_feeder_student->error_code == 0) {
        //             $a_feeder_data = $o_feeder_student->data;
        //             if (count($a_feeder_data) > 1) {
        //                 print('<pre>');
        //                 var_dump($a_feeder_data);exit;
        //             }else if (count($a_feeder_data) == 1){
        //                 $o_feeder_data = $a_feeder_data[0];
        //                 if ($o_student->personal_data_id != $o_feeder_data->id_mahasiswa) {
        //                     $this->Pdm->update_personal_data(['personal_data_id' => $o_feeder_data->id_mahasiswa], $o_student->personal_data_id);
        //                     print($o_student->personal_data_name.'- <i class="text-danger">updated personal_data_id</i><br>');

        //                     if ($o_student->student_id != $o_feeder_data->id_registrasi_mahasiswa) {
        //                         $this->Stm->update_student_data(['student_id' => $o_feeder_data->id_registrasi_mahasiswa], $o_student->student_id);
        //                         print($o_student->personal_data_name.'- <i class="text-danger">updated student_id</i><br>');
        //                     }else{
        //                         print($o_student->personal_data_name.'- <i class="text-primary">loss student_id</i><br>');
        //                     }
        //                 }else{
        //                     if ($o_student->student_id != $o_feeder_data->id_registrasi_mahasiswa) {
        //                         $this->Stm->update_student_data(['student_id' => $o_feeder_data->id_registrasi_mahasiswa], $o_student->student_id);
        //                         print($o_student->personal_data_name.'- <i class="text-danger">updated student_id</i><br>');
        //                     }else{
        //                         print($o_student->personal_data_name.'- <i class="text-primary">loss student_id</i><br>');
        //                     }
        //                 }
        //             }
        //         }else{
        //             print('<pre>');
        //             var_dump($o_feeder_student);exit;
        //         }
        //     }
        // }
    }
}