<?php
class Curriculum_subject extends App_core
{
    function __construct() {
        parent::__construct();
        $this->load->model('academic/Curriculum_model', 'Cum');
        $this->load->model('academic/Subject_model', 'Sbj');
        $this->load->library('FeederAPI', ['mode' => 'production']);
    }

    function sync_curriculum_subject($s_curriculum_id) {
        $mba_curriculum_subject_list = $this->Cum->get_curriculum_subject_filtered([
            'rc.curriculum_id' => $s_curriculum_id,
            'rcs.curriculum_subject_credit >' => 0,
        ]);
        if ($mba_curriculum_subject_list) {
            $s_id_prodi = (is_null($mba_curriculum_subject_list[0]->study_program_main_id)) ? $mba_curriculum_subject_list[0]->study_program_id : $mba_curriculum_subject_list[0]->study_program_main_id;
            foreach ($mba_curriculum_subject_list as $o_currsubject) {
                $s_jenis_matkul = 'B';
                switch ($o_currsubject->curriculum_subject_type) {
                    case 'mandatory':
                        $s_jenis_matkul = 'A';
                        break;
                    case 'extracurricular':
                        $s_jenis_matkul = 'D';
                        break;
                    case 'elective':
                        $s_jenis_matkul = 'B';
                        break;
                    default:
                        $s_jenis_matkul = 'B';
                        break;
                }
                $kurikulum_feeder_matkul = $this->feederapi->post('GetMatkulKurikulum', [
                    'filter' => "id_kurikulum='".$s_curriculum_id."' AND nama_mata_kuliah='".$o_currsubject->subject_name."' AND sks_mata_kuliah = '".$o_currsubject->curriculum_subject_credit."'"
                ]);
                if (($kurikulum_feeder_matkul->error_code == 0) AND (count($kurikulum_feeder_matkul->data) > 0)) {
                    $feeder_subject_data = $kurikulum_feeder_matkul->data[0];
                    if ($feeder_subject_data->id_matkul != $o_currsubject->subject_id) {
                        $s_subject_available_id = '';
                        $s_subject_id_for_update = '';
                        foreach ($kurikulum_feeder_matkul->data as $o_kurrsubject) {
                            $subject_exist_portal = $this->General->get_where('ref_subject', ['subject_id' => $o_kurrsubject->id_matkul]);
                            if (!$subject_exist_portal) {
                                $s_subject_id_for_update = $o_kurrsubject->id_matkul;
                            }
                            else {
                                $s_subject_available_id = $o_kurrsubject->id_matkul;
                            }
                        }
                        
                        if (empty($s_subject_id_for_update)) {
                            // print('for duplicate entries kurikulum subject <pre>');var_dump($kurikulum_feeder_matkul);exit;
                            $this->General->update_data('ref_curriculum_subject', [
                                'subject_id' => $s_subject_available_id
                            ], [
                                'subject_id' => $o_currsubject->subject_id
                            ]);
                        }
                        else {
                            print($o_currsubject->subject_id.' -> '.$s_subject_id_for_update);
                            $this->General->update_data('ref_subject', [
                                'subject_id' => $s_subject_id_for_update
                            ], [
                                'subject_id' => $o_currsubject->subject_id
                            ]);
                        }
                        print('<br>');
                    }
                }
                else if (($kurikulum_feeder_matkul->error_code == 0) AND (count($kurikulum_feeder_matkul->data) == 0)) {
                    // cek data mata kuliah
                    $feeder_detail_subject = $this->feederapi->post('GetDetailMataKuliah', [
                        'filter' => "id_matkul='".$o_currsubject->subject_id."'"
                    ]);
                    if (($feeder_detail_subject->error_code == 0) AND (count($feeder_detail_subject->data) > 0)) {
                        // insert ke kurikulum
                        $insertmatkulkurikulum = $this->feederapi->post('InsertMatkulKurikulum', array(
                            'record' => [
                                'id_kurikulum' => $o_currsubject->curriculum_id,
                                'id_matkul' => $o_currsubject->subject_id,
                                'semester' => ($o_currsubject->semester_id > 8) ? '8' : $o_currsubject->semester_id,
                                'sks_mata_kuliah' => $o_currsubject->curriculum_subject_credit,
                                'sks_tatap_muka' => $o_currsubject->curriculum_subject_credit,
                                'apakah_wajib' => ($o_currsubject->curriculum_subject_type == 'mandatory') ? '1' : '0',
                            ]
                        ));
                        if ($insertmatkulkurikulum->error_code == 0) {
                            print($o_currsubject->subject_name.'_'.$o_currsubject->curriculum_subject_credit.'<br>');
                        }
                        else {
                            // cek error
                            print('<pre>');var_dump($insertmatkulkurikulum);exit;
                        }
                    }
                    else if (($feeder_detail_subject->error_code == 0) AND (count($feeder_detail_subject->data) == 0)) {
                        // cek list mata kuliah
                        $mba_feeder_list_subject = $this->feederapi->post('GetListMataKuliah', [
                            'filter' => "nama_mata_kuliah='".$o_currsubject->subject_name."' AND sks_mata_kuliah='".$o_currsubject->curriculum_subject_credit."'"
                        ]);
                        if (($mba_feeder_list_subject->error_code == 0) AND (count($mba_feeder_list_subject->data) > 0)) {
                            // ambil kode matkul dan push id ke portal
                            $s_id_matkul_ready = '';
                            $s_id_matkul = '';
                            foreach ($mba_feeder_list_subject->data as $subjectfeeder) {
                                $subject_exist_portal = $this->General->get_where('ref_subject', ['subject_id' => $subjectfeeder->id_matkul]);
                                if (!$subject_exist_portal) {
                                    $s_id_matkul = $subjectfeeder->id_matkul;
                                }
                                else {
                                    $s_id_matkul_ready = $subjectfeeder->id_matkul;
                                }
                            }
                            if (empty($s_id_matkul)) {
                                // print('for duplicate entries subject <pre>');var_dump($mba_feeder_list_subject);exit;
                                $insertnewmatkul = $this->feederapi->post('InsertMataKuliah', array(
                                    'record' => [
                                        'kode_mata_kuliah' => $o_currsubject->subject_code,
                                        'nama_mata_kuliah' => $o_currsubject->subject_name,
                                        'id_prodi' => (is_null($o_currsubject->study_program_main_id)) ? $o_currsubject->study_program_id : $o_currsubject->study_program_main_id,
                                        'id_jenis_mata_kuliah' => $s_jenis_matkul,
                                        'sks_mata_kuliah' => $o_currsubject->curriculum_subject_credit,
                                        'sks_tatap_muka' => $o_currsubject->curriculum_subject_credit,
                                    ]
                                ));
                                if ($insertnewmatkul->error_code == 0) {
                                    $s_id_matkul = $insertnewmatkul->data->id_matkul;
                                }
                                else {
                                    print('insert1<pre>');var_dump($insertnewmatkul);exit;
                                }
                            }
                            
                            $this->General->update_data('ref_subject', [
                                'subject_id' => $s_id_matkul
                            ], [
                                'subject_id' => $o_currsubject->subject_id
                            ]);
                            // insert ke kurikulum
                            $insertmatkulkurikulum = $this->feederapi->post('InsertMatkulKurikulum', array(
                                'record' => [
                                    'id_kurikulum' => $o_currsubject->curriculum_id,
                                    'id_matkul' => $s_id_matkul,
                                    'semester' => ($o_currsubject->semester_id > 8) ? '8' : $o_currsubject->semester_id,
                                    'sks_mata_kuliah' => $o_currsubject->curriculum_subject_credit,
                                    'sks_tatap_muka' => $o_currsubject->curriculum_subject_credit,
                                    'apakah_wajib' => ($o_currsubject->curriculum_subject_type == 'mandatory') ? '1' : '0',
                                ]
                            ));
                            if ($insertmatkulkurikulum->error_code == 0) {
                                print($o_currsubject->subject_name.'_'.$o_currsubject->curriculum_subject_credit.'<br>');
                            }
                            else {
                                // cek error
                                print('<pre>');var_dump($insertmatkulkurikulum);exit;
                            }
                        }
                        else if (($mba_feeder_list_subject->error_code == 0) AND (count($mba_feeder_list_subject->data) == 0)) {
                            // create matkul baru
                            $insertnewmatkul = $this->feederapi->post('InsertMataKuliah', array(
                                'record' => [
                                    'kode_mata_kuliah' => $o_currsubject->subject_code,
                                    'nama_mata_kuliah' => $o_currsubject->subject_name,
                                    'id_prodi' => (is_null($o_currsubject->study_program_main_id)) ? $o_currsubject->study_program_id : $o_currsubject->study_program_main_id,
                                    'id_jenis_mata_kuliah' => $s_jenis_matkul,
                                    'sks_mata_kuliah' => $o_currsubject->curriculum_subject_credit,
                                    'sks_tatap_muka' => $o_currsubject->curriculum_subject_credit,
                                ]
                            ));
                            if ($insertnewmatkul->error_code == 0) {
                                // ambil kode matkul dan push id ke portal
                                $s_id_matkul = $insertnewmatkul->data->id_matkul;
                                $this->General->update_data('ref_subject', [
                                    'subject_id' => $s_id_matkul
                                ], [
                                    'subject_id' => $o_currsubject->subject_id
                                ]);
                                // insert ke kurikulum
                                $insertmatkulkurikulum = $this->feederapi->post('InsertMatkulKurikulum', array(
                                    'record' => [
                                        'id_kurikulum' => $o_currsubject->curriculum_id,
                                        'id_matkul' => $s_id_matkul,
                                        'semester' => ($o_currsubject->semester_id > 8) ? '8' : $o_currsubject->semester_id,
                                        'sks_mata_kuliah' => $o_currsubject->curriculum_subject_credit,
                                        'sks_tatap_muka' => $o_currsubject->curriculum_subject_credit,
                                        'apakah_wajib' => ($o_currsubject->curriculum_subject_type == 'mandatory') ? '1' : '0',
                                    ]
                                ));
                                if ($insertmatkulkurikulum->error_code == 0) {
                                    print($o_currsubject->subject_name.'_'.$o_currsubject->curriculum_subject_credit.'<br>');
                                }
                                else {
                                    // cek error
                                    print('<pre>');var_dump($insertmatkulkurikulum);exit;
                                }
                            }
                            else {
                                // cek error
                                print('insert2<pre>');var_dump($insertnewmatkul);exit;
                            }
                        }
                        else {
                            // periksa error
                            print('mba_feeder_list_subject<pre>');var_dump($mba_feeder_list_subject);exit;
                        }
                    }
                    else {
                        // periksa error
                        print('feeder_detail_subject<pre>');var_dump($feeder_detail_subject);exit;
                    }
                }
                else {
                    // periksa error
                    print('<pre>');var_dump($kurikulum_feeder_matkul);exit;
                }
            }
        }
        // print('<pre>');var_dump($mba_curriculum_subject_list);exit;
    }

    function sync_subject() {
        $mba_subject_data = $this->General->get_where('ref_subject');
        if ($mba_subject_data) {
            foreach ($mba_subject_data as $o_subject) {
                $a_get_subject_feeder = $this->feederapi->post('GetDetailMataKuliah', [
                    'filter' => "id_matkul = '{$o_subject->subject_id}'"
                ]);
                // print('<pre>');var_dump($a_get_subject_feeder);exit;
                if ($a_get_subject_feeder->error_code == 0 && count($a_get_subject_feeder->data) == 0) {
                    $mba_prodi_data = $this->General->get_where('ref_study_program', ['study_program_id'=> $o_subject->study_program_id]);
                    $mba_subject_name = $this->General->get_where('ref_subject_name', ['subject_name_id' => $o_subject->subject_name_id]);
                    if(is_null($o_subject->id_jenis_mata_kuliah)){
                        $s_id_jenis_matakuliah = 'B';
                    }
                    else{
                        $s_id_jenis_matakuliah = $o_subject->id_jenis_mata_kuliah;
                    }

                    $a_data_mata_kuliah = [
                        'id_prodi' => (is_null($mba_prodi_data[0]->study_program_main_id)) ? $o_subject->study_program_id : $mba_prodi_data[0]->study_program_main_id,
                        'kode_mata_kuliah' => $o_subject->subject_code,
                        'nama_mata_kuliah' => $mba_subject_name[0]->subject_name,
                        'id_jenis_mata_kuliah' => $s_id_jenis_matakuliah,
                        'sks_mata_kuliah' => $o_subject->subject_credit,
                        'sks_tatap_muka' => $o_subject->subject_credit,
                        'sks_praktek' => $o_subject->subject_credit_p,
                        'sks_praktek_lapangan' => $o_subject->subject_credit_pl,
                        'sks_simulasi' => $o_subject->subject_credit_s
                    ];
                    
                    $o_insert_mata_kuliah = $this->feederapi->post('InsertMataKuliah', [
                        'record' => $a_data_mata_kuliah
                    ]);
        
                    if($o_insert_mata_kuliah->error_code == 0){
                        // print('<pre>');var_dump($o_insert_mata_kuliah);exit;
                        $s_id_matkul = $o_insert_mata_kuliah->data->id_matkul;
                        
                        if (!$this->General->get_where('ref_subject', ['subject_id' => $s_id_matkul])) {
                            $this->Sbj->save_subject_data([
                                'subject_id' => $s_id_matkul
                            ], $o_subject->subject_id);
                        }
                        print('insert '.$mba_subject_name[0]->subject_name.' '.$o_subject->subject_credit.' SKS');
                        print('<br>');
                    }
                    // print('<pre>');var_dump($o_insert_mata_kuliah);exit;
                }
            }
        }
    }
}
