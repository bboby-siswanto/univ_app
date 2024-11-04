<?php
class Activity extends App_core
{
    function __construct()
    {
        parent::__construct('academic');

        $this->load->model('student/Student_model', 'Stm');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('academic/Activity_study_model', 'Asm');
		$this->load->model('academic/Class_group_model', 'Cgm');
		$this->load->model('academic/Curriculum_model', 'Cm');
		$this->load->model('academic/Semester_model', 'Sm');
		$this->load->model('academic/Subject_model', 'Sbj');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('feeder/Feeder_model', 'FeM');
		$this->load->model('employee/Employee_model', 'EeM');
        $this->load->library('FeederAPI', ['mode' => 'production']);
        // $this->load->library('FeederAPI', ['mode' => 'sandbox']);
    }

    public function getKategoriKegiatan()
    {
        $o_data = $this->feederapi->post('GetKategoriKegiatan', [
			'filter' => "id_kategori_kegiatan = '110402 '"
        ]);
        
        print('<pre>');
        var_dump($o_data);
    }

    public function test()
    {
        $a_feeder_checker = $this->feederapi->post('GetListBimbingMahasiswa', array(
            'filter' => "id_aktivitas = 'a31bf433-6ea0-4bf0-8152-823933105a07' AND id_dosen = '7f497965-c151-4c4e-b250-75d06e72ea3f'"
            // 'filter' => "id_aktivitas = 'a31bf433-6ea0-4bf0-8152-823933105a07'"
        ));
        // $a_post_delete_data = $this->sync_penguji_aktvitas('c02623dc-0c17-420a-93b2-8c2e3d1f4e5e', 'delete');
        print('<pre>');
        var_dump($a_feeder_checker);
    }

    public function check_dosen($s_id_dosen)
    {
        $a_feeder_checker = $this->feederapi->post('GetListDosen', array(
            'filter' => "id_dosen = '$s_id_dosen'"
        ));

        if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
            return false;
        }
        else {
            return true;
        }
        // print('<pre>');var_dump($a_feeder_checker);exit;
    }

    function cek_dosen() {
        $a_id_dosen = ['365330c2-e11f-4c26-8a71-0fba4a92406f','133febc3-69d6-4822-a601-511a6f1a13bf','89edb30a-2eed-49b2-906c-edc0cba570a6','7f497965-c151-4c4e-b250-75d06e72ea3f','43cf7e29-9d1b-48fe-86c8-9319f95a5d3a','861fc66f-e39a-4975-8009-1424508e97bc','fef6a1f1-b123-4f7d-978f-2184a396c1a1','42342bc3-a09d-4d06-aa03-32cf9c9342d7','6180189d-c45b-4ae4-bbc1-a3d99ce0d095','fa4a91d9-87e8-4394-885e-71593af01cfc','adfbca82-4615-428c-92f5-8328ccffaf0d','59833f8c-7ba3-421b-a4ba-74d3aa8699da','76f0449d-1407-4220-a0bc-174b4354a3ef','4009403f-01ef-413c-9480-bf04b12f2e7d','6e64cb0a-3327-4db4-8970-f79b6bf42919','123b9806-fe93-49e0-9808-2cb2fb922479','264943ee-2298-4714-ab72-d249926bd65d','0fc353e1-28b3-4522-9da2-57c588088285','a7b8483b-0b1f-4045-ade1-cbca8b5769bc','6aa4f131-b15a-4f05-95c0-c3cb7a476900','dcc4c64e-7dea-4468-aff5-32bdabd99a0e','a0cc9e9d-9b78-4926-893d-fc6fd4ba496f','f7146e35-431f-4426-87c4-6c6f1439d835','dd1c9811-2f67-41b5-9cf5-b8db5f09d35e','4d8d45a3-6745-4d61-b6d6-b3034233a7ff','1a6abebe-21b6-42a5-9937-ec553e164067','be18ea0b-dd4a-4304-b45b-9ef9949af997','58344ee8-82a6-416b-9d2b-9d0166393e94','b075e800-cccc-4538-9bec-02e149890432','6c054e0e-6cca-4d43-b477-3f8ec76a5a50','607b594b-57a1-4582-8cd9-f054c78b7f37','3ccb60a6-1c80-490d-94d5-3f6636ffec6e','81a7fb11-1d28-4815-9612-7c5dfe696b9c','becf5d5d-45b6-4cef-9225-53a6ea7c88cd','89edb30a-2eed-49b2-906c-edc0cba570a6','7f497965-c151-4c4e-b250-75d06e72ea3f','43cf7e29-9d1b-48fe-86c8-9319f95a5d3a','861fc66f-e39a-4975-8009-1424508e97bc','fef6a1f1-b123-4f7d-978f-2184a396c1a1','42342bc3-a09d-4d06-aa03-32cf9c9342d7','fa4a91d9-87e8-4394-885e-71593af01cfc','14e13d52-ee7a-45a6-8fcf-40d2fbeddb9e','59833f8c-7ba3-421b-a4ba-74d3aa8699da','76f0449d-1407-4220-a0bc-174b4354a3ef','dbfa42ab-e999-41b4-b232-8391509cded5','4009403f-01ef-413c-9480-bf04b12f2e7d','6e64cb0a-3327-4db4-8970-f79b6bf42919','123b9806-fe93-49e0-9808-2cb2fb922479','0fc353e1-28b3-4522-9da2-57c588088285','6aa4f131-b15a-4f05-95c0-c3cb7a476900','dcc4c64e-7dea-4468-aff5-32bdabd99a0e','a0cc9e9d-9b78-4926-893d-fc6fd4ba496f','f7146e35-431f-4426-87c4-6c6f1439d835','dd1c9811-2f67-41b5-9cf5-b8db5f09d35e','4d8d45a3-6745-4d61-b6d6-b3034233a7ff','1a6abebe-21b6-42a5-9937-ec553e164067','be18ea0b-dd4a-4304-b45b-9ef9949af997','58344ee8-82a6-416b-9d2b-9d0166393e94','b075e800-cccc-4538-9bec-02e149890432','6c054e0e-6cca-4d43-b477-3f8ec76a5a50','20eea379-07d5-47b1-a606-e5de37c71baf','607b594b-57a1-4582-8cd9-f054c78b7f37','3ccb60a6-1c80-490d-94d5-3f6636ffec6e','81a7fb11-1d28-4815-9612-7c5dfe696b9c','2cc9c01a-88de-42e9-84bc-3368171c6635','becf5d5d-45b6-4cef-9225-53a6ea7c88cd'];
        foreach ($a_id_dosen as $s_id_dosen) {
            $a_feeder_checker = $this->feederapi->post('GetListDosen', array(
                'filter' => "id_dosen = '$s_id_dosen'"
            ));
            if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
                print('<pre>');var_dump($a_feeder_checker);
                print('<br>');
            }
        }
    }

    public function sync_penguji_aktvitas($s_activity_lecturer_id, $s_action = 'insert', $b_force_link = false)
    {
        $mbo_activity_lecturer = $this->Asm->get_activity_lecturer(['al.activity_lecturer_id' => $s_activity_lecturer_id])[0];
        $s_error_desc = '';

        if ($mbo_activity_lecturer) {
            $b_dosen_exists = $this->check_dosen($mbo_activity_lecturer->employee_id);
            if (!$b_dosen_exists) {
                if ($b_force_link) {
                    print('<pre>');var_dump("id dosen salah!");exit;
                }

                return "Dosen tidak ditemukan!";
            }

            $a_feeder_data = [
                'id_aktivitas' => $mbo_activity_lecturer->activity_study_id,
                'id_kategori_kegiatan' => $mbo_activity_lecturer->id_kategori_kegiatan,
                'id_dosen' => $mbo_activity_lecturer->employee_id,
                'penguji_ke' => $mbo_activity_lecturer->activity_lecturer_sequence
            ];

            $a_feeder_checker = $this->feederapi->post('GetListUjiMahasiswa', array(
                'filter' => "id_aktivitas = '{$mbo_activity_lecturer->activity_study_id}' AND id_dosen = '{$mbo_activity_lecturer->employee_id}'"
            ));

            if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
                $a_post_save_data = $this->feederapi->post('InsertUjiMahasiswa', array(
                    'record' => $a_feeder_data
                ));
                // print('<pre>');var_dump($a_post_save_data);exit;

                if ($a_post_save_data->error_code == 0) {
                    $a_data = $a_post_save_data->data;
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_data);
                    }

                    $a_activity_update_data = array(
                        'activity_lecturer_id' => str_replace("'", "", $a_data->id_uji),
                        'feeder_sync' => $a_post_save_data->error_code
                    );
                }else{
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_post_save_data);exit;
                    }
                    $this->FeM->push_error_dikti($a_post_save_data);
                    $a_activity_update_data = array(
                        'feeder_sync' => $a_post_save_data->error_code
                    );

                    $s_error_desc = $a_post_save_data->error_desc;
                }

                $this->Asm->save_lecturer_activity($a_activity_update_data, $mbo_activity_lecturer->activity_lecturer_id);

            }else if(($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) >= 0)) {
                if($s_action == 'delete') {
                    $a_post_delete_data = $this->feederapi->post('DeleteUjiMahasiswa', array(
                        'key' => [
                            'id_uji' => $s_activity_lecturer_id
                        ]
                    ));
    
                    if ($a_post_delete_data->error_code != 0) {
                        if ($b_force_link) {
                            print('<pre>');var_dump($a_post_delete_data);exit;
                        }
                        $this->FeM->push_error_dikti($a_post_delete_data);
                        // $a_activity_update_data = array(
                        //     'feeder_sync' => $a_post_delete_data->error_code
                        // );
    
                        $s_error_desc = $a_post_delete_data->error_desc;
                    }
                }
            }else if ($a_feeder_checker->error_code != 0) {
                if ($b_force_link) {
                    print('<pre>');var_dump($a_feeder_checker);exit;
                }
                $this->FeM->push_error_dikti($a_feeder_checker);
            }
        }

        return $s_error_desc;
        // exit;
    }

    public function sync_pembimbing_aktvitas($s_activity_lecturer_id, $s_action = 'insert', $b_force_link = false)
    {
        $mbo_activity_lecturer = $this->Asm->get_activity_lecturer(['al.activity_lecturer_id' => $s_activity_lecturer_id])[0];
        $s_error_desc = '';
        
        if ($mbo_activity_lecturer) {
            $b_dosen_exists = $this->check_dosen($mbo_activity_lecturer->employee_id);
            if (!$b_dosen_exists) {
                if ($b_force_link) {
                    print('<pre>');var_dump("id dosen salah!");exit;
                }

                return "Dosen tidak ditemukan!";
            }

            $a_feeder_data = [
                'id_aktivitas' => $mbo_activity_lecturer->activity_study_id,
                'id_kategori_kegiatan' => $mbo_activity_lecturer->id_kategori_kegiatan,
                // 'id_dosen' => '123',
                'id_dosen' => $mbo_activity_lecturer->employee_id,
                'pembimbing_ke' => $mbo_activity_lecturer->activity_lecturer_sequence
            ];

            $a_feeder_checker = $this->feederapi->post('GetListBimbingMahasiswa', array(
                'filter' => "id_aktivitas = '{$mbo_activity_lecturer->activity_study_id}' AND id_dosen = '{$mbo_activity_lecturer->employee_id}'"
            ));

            if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
                $a_post_save_data = $this->feederapi->post('InsertBimbingMahasiswa', array(
                    'record' => $a_feeder_data
                ));

                if ($a_post_save_data->error_code == 0) {
                    $a_data = $a_post_save_data->data;
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_data);
                    }
                    $a_activity_update_data = array(
                        'activity_lecturer_id' => str_replace("'", "", $a_data->id_bimbing_mahasiswa),
                        'feeder_sync' => $a_post_save_data->error_code
                    );

                    $this->send_notification_telegram(json_encode($a_activity_update_data));
                    $this->Asm->save_lecturer_activity($a_activity_update_data, $mbo_activity_lecturer->activity_lecturer_id);
                }else{
                    $this->FeM->push_error_dikti($a_post_save_data);
                    $a_activity_update_data = array(
                        'feeder_sync' => $a_post_save_data->error_code
                    );
                    $this->Asm->save_lecturer_activity($a_activity_update_data, $mbo_activity_lecturer->activity_lecturer_id);

                    if ($b_force_link) {
                        print('<pre>');var_dump($a_post_save_data);exit;
                    }
                    $s_error_desc = $a_post_save_data->error_desc;
                }

            }else if(($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) >= 0)) {
                

                if($s_action == 'delete') {
                    $a_post_delete_data = $this->feederapi->post('DeleteBimbingMahasiswa', array(
                        'key' => [
                            'id_bimbing_mahasiswa' => $s_activity_lecturer_id
                        ]
                    ));
    
                    if ($a_post_delete_data->error_code != 0) {
                        $this->FeM->push_error_dikti($a_post_delete_data);
                        $a_activity_update_data = array(
                            'feeder_sync' => $a_post_delete_data->error_code
                        );
                        if ($b_force_link) {
                            print('<pre>');var_dump($a_post_delete_data);exit;
                        }
                        $this->Asm->save_lecturer_activity($a_activity_update_data, $mbo_activity_lecturer->activity_lecturer_id);
    
                        $s_error_desc = $a_post_delete_data->error_desc;
                    }
                }
            }else if ($a_feeder_checker->error_code != 0) {
                $this->FeM->push_error_dikti($a_feeder_checker);
                if ($b_force_link) {
                    print('<pre>');var_dump($a_feeder_checker);exit;
                }
            }
        }

        return $s_error_desc;
        // exit;
    }

    public function sync_peserta_aktivitas($s_activity_student_id, $s_action = 'insert', $b_force_link = false)
    {
        $mbo_activities_student_list = $this->Asm->get_activity_student_data(['activity_student_id' => $s_activity_student_id])[0];
        $s_error_desc = '';

        if ($mbo_activities_student_list) {
            $a_feeder_data = [
                'id_aktivitas' => $mbo_activities_student_list->activity_study_id,
                'id_registrasi_mahasiswa' => $mbo_activities_student_list->student_id,
                'jenis_peran' => $mbo_activities_student_list->role_type
            ];

            $a_feeder_checker = $this->feederapi->post('GetListAnggotaAktivitasMahasiswa', array(
                'filter' => "id_aktivitas = '{$mbo_activities_student_list->activity_study_id}' AND id_registrasi_mahasiswa = '{$mbo_activities_student_list->student_id}'"
            ));

            if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
                if ($s_action != 'delete') {
                    $a_post_save_data = $this->feederapi->post('InsertAnggotaAktivitasMahasiswa', array(
                        'record' => $a_feeder_data
                    ));
    
                    if ($a_post_save_data->error_code == 0) {
                        $a_data = $a_post_save_data->data;
                        $a_activity_update_data = array(
                            'activity_student_id' => str_replace("'", "", $a_data->id_anggota),
                            'feeder_sync' => $a_post_save_data->error_code
                        );
                    }else{
                        $this->FeM->push_error_dikti($a_post_save_data);
                        $a_activity_update_data = array(
                            'feeder_sync' => $a_post_save_data->error_code
                        );
    
                        $s_error_desc = $a_post_save_data->error_desc;
                        if ($b_force_link) {
                            print('<pre>');var_dump($a_post_save_data);exit;
                        }
                    }
    
                    $this->Asm->save_student_activity($a_activity_update_data, $mbo_activities_student_list->activity_student_id);
                    // return 'insert';
                }
            }else if(($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) >= 0)) {
                

                if($s_action == 'delete') {
                    $a_post_delete_data = $this->feederapi->post('DeleteAnggotaAktivitasMahasiswa', array(
                        'key' => [
                            'id_anggota' => $s_activity_student_id
                        ]
                    ));
    
                    if ($a_post_delete_data->error_code != 0) {
                        $this->FeM->push_error_dikti($a_post_delete_data);
                        $a_activity_update_data = array(
                            'feeder_sync' => $a_post_delete_data->error_code
                        );
                        $this->Asm->save_student_activity($a_activity_update_data, $mbo_activities_student_list->activity_student_id);
    
                        $s_error_desc = $a_post_delete_data->error_desc;
                        if ($b_force_link) {
                            print('<pre>');var_dump($a_post_delete_data);exit;
                        }
                    }
                }
            }else if ($a_feeder_checker->error_code != 0) {
                $this->FeM->push_error_dikti($a_feeder_checker);
                if ($b_force_link) {
                    print('<pre>');var_dump($a_feeder_checker);exit;
                }
            }
        }
        else {
            $s_error_desc = 'Activity not found!';
        }

        return $s_error_desc;
        // exit;
    }

    public function check()
    {
        $this->load->model('academic/Semester_model', 'Smm');
        $a_student_data = $this->Stm->get_student_filtered(['academic_year_id' => '2020'], ['active']);
        $i = 0;
        foreach ($a_student_data as $student) {
            $i++;
            $mbo_student_semester_data = $this->Smm->get_student_start_semester(false, array(
                'st.student_id' => $student->student_id
            ));

            if (($mbo_student_semester_data) AND ($mbo_student_semester_data->academic_year_id == '2020')) {
                // 
            }else{
                print('<pre>');
                var_dump($mbo_student_semester_data);
            }
        }

        print($i);

        // print('<pre>');
        // var_dump($a_student_data);exit;
    }

    public function sync_activity($s_activity_id, $b_force_link = false)
    {
        $s_id_aktivitas = $s_activity_id;
        $mbo_activities_data = $this->Asm->get_activity_data(['das.activity_study_id' => $s_activity_id])[0];
        $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mbo_activities_data->study_program_id]);
        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
        }
        $s_error_desc = '';

        if ($mbo_activities_data) {
            $a_feeder_data = array(
                'program_mbkm' => '0',
                'jenis_anggota' => $mbo_activities_data->activity_member_type,
                'id_jenis_aktivitas' => $mbo_activities_data->id_jenis_aktivitas_mahasiswa,
                'id_prodi' => $mba_study_program_data[0]->study_program_id,
                'id_semester' => $mbo_activities_data->academic_year_id.$mbo_activities_data->semester_type_id,
                'judul' => $mbo_activities_data->activity_title,
                'keterangan' => $mbo_activities_data->activity_remarks,
                'lokasi' => $mbo_activities_data->activity_location,
                'sk_tugas' => $mbo_activities_data->activity_sk_number,
                'tanggal_sk_tugas' => $mbo_activities_data->activity_sk_date
            );
            
            $a_feeder_checker = $this->feederapi->post('GetListAktivitasMahasiswa', array(
                'filter' => "id_aktivitas = '{$mbo_activities_data->activity_study_id}'"
            ));
            // print('<pre>');var_dump($a_feeder_checker);exit;

            if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) > 0)) {
                $a_post_save_data = $this->feederapi->post('UpdateAktivitasMahasiswa', array(
                    'key' => ['id_aktivitas' => $mbo_activities_data->activity_study_id, 'id_semester' => '20222'],
                    'record' => $a_feeder_data
                ));
                
                // $a_post_save_data = $this->feederapi->post('UpdateAktivitasMahasiswa', $a_feeder_data);

                if ($a_post_save_data->error_code != 0) {
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_post_save_data);exit;
                    }

                    $this->FeM->push_error_dikti($a_post_save_data);
                    $a_activity_update_data = array(
                        'feeder_sync' => $a_post_save_data->error_code
                    );

                    $s_error_desc = $a_post_save_data->error_desc;

                    $this->Asm->save_activity_study($a_activity_update_data, $mbo_activities_data->activity_study_id);
                }
            }else if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
                $a_post_save_data = $this->feederapi->post('InsertAktivitasMahasiswa', array(
                    'record' => $a_feeder_data
                ));

                if ($a_post_save_data->error_code == 0) {
                    $a_data = $a_post_save_data->data;
                    $s_id_aktivitas = $a_data->id_aktivitas;
                    $a_activity_update_data = array(
                        'activity_study_id' => $a_data->id_aktivitas,
                        'feeder_sync' => $a_post_save_data->error_code
                    );
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_data);
                    }
                }else{
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_post_save_data);exit;
                    }
                    $this->FeM->push_error_dikti($a_post_save_data);
                    $a_activity_update_data = array(
                        'feeder_sync' => $a_post_save_data->error_code
                    );

                    $s_error_desc = $a_post_save_data->error_desc;
                }

                $this->Asm->save_activity_study($a_activity_update_data, $mbo_activities_data->activity_study_id);
            }else{
                if ($b_force_link) {
                    print('<pre>');var_dump($a_feeder_checker);exit;
                }
                $this->FeM->push_error_dikti($a_feeder_checker);
                $s_error_desc = $a_feeder_checker->error_desc;
            }

        }
        else {
            if ($b_force_link) {
                print('<pre>');var_dump('activity tidak ditemukan');exit;
            }
        }

        return [
            'id_aktivitas' => $s_id_aktivitas,
            'error_message' => $s_error_desc
        ];
        // exit;
    }

    // public function sync_activity_from_feeder()
    // {
    //     $a_activity_data = $this->feederapi->post('GetListAktivitasMahasiswa');
    //     foreach ($a_activity_data->data as $o_activity_study) {
    //         // $this->iuli_lib->print_pre($o_activity_study);
    //         print(substr($o_activity_study->id_semester, 4, 1));
    //         print('<br>');
    //         $a_activity_data = [
    //             'activity_study_id' => $o_activity_study->id_aktivitas,
    //             'academic_year_id' => substr($o_activity_study->id_semester, 0, 4),
    //             'semester_type_id' => substr($o_activity_study->id_semester, 4, 1),
    //             'study_program_id' => $a_activity_data->id_prodi,
    //             'id_jenis_aktivitas_mahasiswa' => $o_activity_study->id_jenis_aktivitas,
    //             'activity_member_type' => $o_activity_study->jenis_anggota,
    //             'activity_title' => $o_activity_study->jenis_anggota,
    //         ];
    //     }
    //     // $this->iuli_lib->print_pre($a_activity_data);
    // }

    public function sync_student_activity($s_student_id, $s_academic_year_id, $s_semester_type_id, $s_dikti_semester_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);

        $mba_supplement_data = $this->Scm->get_supplement([
            'student_id' => $s_student_id,
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id,
            // 'supplement_category' => 'positive',
        ]);

        if ($mba_supplement_data) {
            foreach ($mba_supplement_data as $o_supplement) {
                $a_data = [
                    'jenis_anggota' => 0,
                    'id_jenis_aktivitas' => '',
                    'id_prodi' => $mbo_student_data->s_study_program_id,
                    'id_semester' => $s_dikti_semester_id,
                    // 'judul' => '',
                    // 'keterangan' => '',
                    // 'lokasi' => '',
                    // 'sk_tugas' => '',
                    // 'tanggal_sk_tugas' => ''
                ];
            }
        }
    }

    function sync_all() {
        $mba_activity_data = $this->General->get_where('dt_activity_study', ['academic_year_id' => '2022', 'semester_type_id' => '2']);
        if ($mba_activity_data) {
            foreach ($mba_activity_data as $o_activity) {
                $this->sync_activity_bundle($o_activity->activity_study_id, true);
                sleep(1);
            }
        }
    }

    public function sync_activity_bundle($s_activity_id, $b_force_link = false)
    {
        $mbo_activities_data = $this->Asm->get_activity_data(['das.activity_study_id' => $s_activity_id])[0];
        $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mbo_activities_data->study_program_id]);
        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
        }
        $s_error_desc = '';

        if ($mbo_activities_data) {
            $a_feeder_data = array(
                'program_mbkm' => '0',
                'jenis_anggota' => $mbo_activities_data->activity_member_type,
                'id_jenis_aktivitas' => $mbo_activities_data->id_jenis_aktivitas_mahasiswa,
                'id_prodi' => $mba_study_program_data[0]->study_program_id,
                'id_semester' => $mbo_activities_data->academic_year_id.$mbo_activities_data->semester_type_id,
                'judul' => $mbo_activities_data->activity_title,
                'keterangan' => $mbo_activities_data->activity_remarks,
                'lokasi' => $mbo_activities_data->activity_location,
                'sk_tugas' => $mbo_activities_data->activity_sk_number,
                'tanggal_sk_tugas' => $mbo_activities_data->activity_sk_date
            );
            
            $a_feeder_checker = $this->feederapi->post('GetListAktivitasMahasiswa', array(
                'filter' => "id_aktivitas = '{$mbo_activities_data->activity_study_id}'"
            ));

            // if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) > 0)) {
            //     $a_post_save_data = $this->feederapi->post('UpdateAktivitasMahasiswa', array(
            //         'key' => ['id_aktivitas' => $mbo_activities_data->activity_study_id],
            //         'record' => $a_feeder_data
            //     ));

            //     if ($a_post_save_data->error_code != 0) {
            //         if ($b_force_link) {
            //             print('<pre>');var_dump($a_post_save_data);exit;
            //         }

            //         $this->FeM->push_error_dikti($a_post_save_data);
            //         $a_activity_update_data = array(
            //             'feeder_sync' => $a_post_save_data->error_code
            //         );

            //         $s_error_desc = $a_post_save_data->error_desc;

            //         $this->Asm->save_activity_study($a_activity_update_data, $mbo_activities_data->activity_study_id);
            //     }
            // }
            // else if (($a_feeder_checker->error_code == 0) AND (count($a_feeder_checker->data) == 0)) {
            if ($a_feeder_checker->error_code == 0) {
                if (count($a_feeder_checker->data) > 0) {
                    $a_feeder_anggota = $this->feederapi->post('GetListAnggotaAktivitasMahasiswa', array(
                        'filter' => "id_aktivitas = '{$mbo_activities_data->activity_study_id}'"
                    ));
                    $a_feeder_bimbing = $this->feederapi->post('GetListBimbingMahasiswa', array(
                        'filter' => "id_aktivitas = '{$mbo_activities_data->activity_study_id}'"
                    ));
                    $a_feeder_uji = $this->feederapi->post('GetListUjiMahasiswa', array(
                        'filter' => "id_aktivitas = '{$mbo_activities_data->activity_study_id}'"
                    ));

                    if (($a_feeder_anggota->error_code == 0) AND (count($a_feeder_anggota->data) > 0)) {
                        foreach ($a_feeder_anggota->data as $result) {
                            $a_post_delete_data = $this->feederapi->post('DeleteAnggotaAktivitasMahasiswa', array(
                                'key' => [
                                    'id_anggota' => $result->id_anggota
                                ]
                            ));
                        }
                    }

                    if (($a_feeder_bimbing->error_code == 0) AND (count($a_feeder_bimbing->data) > 0)) {
                        foreach ($a_feeder_bimbing->data as $result) {
                            $a_post_delete_data = $this->feederapi->post('DeleteBimbingMahasiswa', array(
                                'key' => [
                                    'id_bimbing_mahasiswa' => $result->id_bimbing_mahasiswa
                                ]
                            ));
                        }
                    }

                    if (($a_feeder_uji->error_code == 0) AND (count($a_feeder_uji->data) > 0)) {
                        foreach ($a_feeder_uji->data as $result) {
                            $a_post_delete_data = $this->feederapi->post('DeleteUjiMahasiswa', array(
                                'key' => [
                                    'id_uji' => $result->id_uji
                                ]
                            ));
                        }
                    }
                    
                    $a_post_delete_data = $this->feederapi->post('DeleteAktivitasMahasiswa', array(
                        'key' => [
                            'id_aktivitas' => $mbo_activities_data->activity_study_id
                        ]
                    ));
                }
                
                $a_post_save_data = $this->feederapi->post('InsertAktivitasMahasiswa', array(
                    'record' => $a_feeder_data
                ));

                if ($a_post_save_data->error_code == 0) {
                    $a_data = $a_post_save_data->data;
                    $a_activity_update_data = array(
                        'activity_study_id' => $a_data->id_aktivitas,
                        'feeder_sync' => $a_post_save_data->error_code
                    );

                    $s_activity_id = $a_data->id_aktivitas;
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_data);
                    }
                }else{
                    if ($b_force_link) {
                        print('<pre>');var_dump($a_post_save_data);exit;
                    }
                    $this->FeM->push_error_dikti($a_post_save_data);
                    $a_activity_update_data = array(
                        'feeder_sync' => $a_post_save_data->error_code
                    );

                    $s_error_desc = $a_post_save_data->error_desc;
                }

                $this->Asm->save_activity_study($a_activity_update_data, $mbo_activities_data->activity_study_id);
            }else{
                if ($b_force_link) {
                    print('<pre>');var_dump($a_feeder_checker);exit;
                }
                $this->FeM->push_error_dikti($a_feeder_checker);
                $s_error_desc = $a_feeder_checker->error_desc;
            }

            $mba_aktivitas_mahasiswa = $this->Asm->get_activity_student_data(['dast.activity_study_id' => $s_activity_id]);
            $mba_aktivitas_pembimbing = $this->Asm->get_activity_lecturer(['al.activity_study_id' => $s_activity_id, 'activity_lecturer_type' => 'adviser']);
            $mba_aktivitas_pemnguji = $this->Asm->get_activity_lecturer(['al.activity_study_id' => $s_activity_id, 'activity_lecturer_type' => 'examiner']);
            if ($mba_aktivitas_mahasiswa) {
                foreach ($mba_aktivitas_mahasiswa as $o_student_activity) {
                    $this->sync_peserta_aktivitas($o_student_activity->activity_student_id);
                }
            }

            if ($mba_aktivitas_pembimbing) {
                foreach ($mba_aktivitas_pembimbing as $o_pembimbing_activity) {
                    // print('<pre>');var_dump($o_pembimbing_activity);exit;
                    if (!in_array($o_pembimbing_activity->employee_id, ['133febc3-69d6-4822-a601-511a6f1a13bf', 'a7b8483b-0b1f-4045-ade1-cbca8b5769bc'])) {
                        $this->sync_pembimbing_aktvitas($o_pembimbing_activity->activity_lecturer_id, 'insert', true);
                    }
                }
            }
            
            if ($mba_aktivitas_pemnguji) {
                foreach ($mba_aktivitas_pemnguji as $o_penguji_activity) {
                    if (!in_array($o_penguji_activity->employee_id, ['133febc3-69d6-4822-a601-511a6f1a13bf', 'a7b8483b-0b1f-4045-ade1-cbca8b5769bc'])) {
                        $this->sync_penguji_aktvitas($o_penguji_activity->activity_lecturer_id, 'insert', true);
                    }
                }
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Activity not found!'];
        }

        if ($b_force_link) {
            // print('<pre>');var_dump($a_return);exit;
        }
        else {
            return $a_return;
        }
    }
}
