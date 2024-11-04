<?php
class Report extends App_core
{
    function __construct() {
        parent::__construct();
        $this->load->model('academic/Curriculum_model', 'Cum');
        $this->load->library('FeederAPI', ['mode' => 'production']);
    }

    function student_feeder() {
        $this->a_page_data['body'] = $this->load->view('report/student', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }
    function kurikulum_matkul() {
        $this->a_page_data['body'] = $this->load->view('report/kurikulum_matkul', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function force_update_feeder() {
        $a_insertkurikulum_data = [
            'nama_kurikulum' => 'CURRICULUM CHE 2019',
            'id_prodi' => '6ce5bc8b-10f5-456d-855d-aef18dc641f4',
            'id_semester' => '20191',
            'jumlah_sks_lulus' => "111"
        ];
        $o_insertkurikulum = $this->feederapi->post('UpdateKurikulum', array(
            'record' => $a_insertkurikulum_data,
            'key' => [
                'id_kurikulum' => '438d70c1-8f3a-4ce7-8bd2-8e7bfa704246'
            ]
        ));
        print('<pre>');var_dump($o_insertkurikulum);exit;
    }

    function test_sync_subject() {
        $insertnewmatkul = $this->feederapi->post('InsertMataKuliah', array(
            'record' => [
                'kode_mata_kuliah' => 'VBAM0-09001',
                'nama_mata_kuliah' => 'Value Based Management',
                'id_prodi' => 'c395b273-5acb-41c6-9d44-bccabd93d312',
                'id_jenis_mata_kuliah' => 'A',
                'sks_mata_kuliah' => '2',
                'sks_tatap_muka' => '2',
            ]
        ));
        if ($insertnewmatkul->error_code == 0) {
            // ambil kode matkul dan push id ke portal
            // insert ke kurikulum
            print('<pre>');var_dump($insertnewmatkul->data->id_matkul);exit;
        }
        else {
            // cek error
            print('<pre>');var_dump($insertnewmatkul);exit;
        }
    }

    function sync_curriculum() {
        $mba_kurikulum_list = $this->validate_kurikulum([
            'sp.study_program_id' => '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89'
        ]);
        if ($mba_kurikulum_list) {
            foreach ($mba_kurikulum_list as $o_kurikulum) {
                if ($o_kurikulum->kurikulum_feeder == 'kosong') {
                    print($o_kurikulum->curriculum_id);
                    $a_insertkurikulum_data = [
                        'nama_kurikulum' => "ABCDEF",
                        // 'id_jenjang_pendidikan' => '31',
                        // 'id_prodi' => (is_null($o_kurikulum->study_program_main_id)) ? $o_kurikulum->study_program_id : $o_kurikulum->study_program_main_id,
                        'id_prodi' => '903eb8ee-159e-406b-8f7e-38d63a961ea4',
                        // 'id_semester' => $o_kurikulum->valid_academic_year.'1',
                        'id_semester' => '20231',
                        'jumlah_sks_lulus' => "144",
                        'jumlah_sks_wajib' => "144",
                        'jumlah_sks_pilih' => '0'
                    ];

                    $o_insertkurikulum = $this->feederapi->post('InsertKurikulum', array(
                        'record' => $a_insertkurikulum_data
                    ));
                    if($o_insertkurikulum->error_code == 0) {
                        print('<pre>');var_dump($o_insertkurikulum->data);
                    }
                    else {
                        print('<pre>');var_dump($o_insertkurikulum);
                    }
                    print(strlen($o_kurikulum->curriculum_name));
                    print('<br>');
                }
            }
        }
        // print('<pre>');var_dump($mba_kurikulum_list);exit;
    }

    function validate_kurikulum($a_clause = false) {
        $mba_kurikulum_iuli = $this->Cum->get_curriculum_list_filter($a_clause, [
            'sp.study_program_name_feeder' => 'ASC',
            'cr.valid_academic_year' => 'ASC'
        ]);
        if ($mba_kurikulum_iuli) {
            foreach ($mba_kurikulum_iuli as $key => $o_curriculum) {
                $mba_curriculum_subject_list = $this->Cum->get_curriculum_subject_filtered([
                    'rc.curriculum_id' => $o_curriculum->curriculum_id,
                    'rcs.curriculum_subject_credit >' => 0,
                ]);
                $d_total_sks = 0;
                $d_total_subject = 0;

                $kurikulum_iuli_subject = [];
                $key_kurikulum_iuli_subject = [];
                if ($mba_curriculum_subject_list) {
                    foreach ($mba_curriculum_subject_list as $o_curriculum_subject) {
                        if ($o_curriculum_subject->curriculum_subject_credit > 0) {
                            $d_total_subject++;
                            $d_total_sks += $o_curriculum_subject->curriculum_subject_credit;

                            array_push($kurikulum_iuli_subject, [
                                'subject_id' => $o_curriculum_subject->subject_id,
                                'subject_code' => $o_curriculum_subject->subject_code,
                                'subject_name' => $o_curriculum_subject->subject_name,
                                'subject_credit' => $o_curriculum_subject->curriculum_subject_credit
                            ]);
                            array_push($key_kurikulum_iuli_subject, $o_curriculum_subject->subject_name.'#'.$o_curriculum_subject->curriculum_subject_credit);
                        }
                    }
                }
                if ($d_total_subject == 0) {
                    unset($mba_kurikulum_iuli[$key]);
                }
                else {
                    $mba_kurikulum_detail = $this->feederapi->post('GetDetailKurikulum', [
                        'filter' => "id_kurikulum='".$o_curriculum->curriculum_id."'"
                    ]);
                    $kurikulum_feeder = [];
                    $kurikulum_feeder_subject = [];
                    $key_kurikulum_feeder_subject = [];
                    $s_feeder_status = 'Not Available';
                    if (($mba_kurikulum_detail->error_code == 0) AND (count($mba_kurikulum_detail->data) > 0)) {
                        $kurikulum_feeder_data = $mba_kurikulum_detail->data[0];
                        $s_feeder_status = 'Available';
                        $kurikulum_feeder_matkul = $this->feederapi->post('GetMatkulKurikulum', [
                            'filter' => "id_kurikulum='".$o_curriculum->curriculum_id."'"
                        ]);
                        $kurikulum_feeder['curriculum_name'] = $kurikulum_feeder_data->nama_kurikulum;
                        $kurikulum_feeder['valid_academic_year'] = substr($kurikulum_feeder_data->id_semester, 0, 4);

                        if (($kurikulum_feeder_matkul->error_code == 0) AND (count($kurikulum_feeder_matkul->data) > 0)) {
                            // print('<pre>');var_dump($kurikulum_feeder_matkul);exit;
                            $kurikulum_feeder_subject_data = $kurikulum_feeder_matkul->data;
                            foreach ($kurikulum_feeder_subject_data as $key => $o_kurikulum_subject) {
                                array_push($kurikulum_feeder_subject, [
                                    'subject_id' => $o_kurikulum_subject->id_matkul,
                                    'subject_code' => $o_kurikulum_subject->kode_mata_kuliah,
                                    'subject_name' => $o_kurikulum_subject->nama_mata_kuliah,
                                    'subject_credit' => intval($o_kurikulum_subject->sks_mata_kuliah)
                                ]);
                                array_push($key_kurikulum_feeder_subject, $o_kurikulum_subject->nama_mata_kuliah.'#'.intval($o_kurikulum_subject->sks_mata_kuliah));
                            }
                        }
                    }
                    $kurikulum_iuli = [
                        'curriculum_name' => $o_curriculum->curriculum_name,
                        'valid_academic_year' => $o_curriculum->valid_academic_year
                    ];
                    $valid_kurikulum_data = array_diff($kurikulum_iuli, $kurikulum_feeder);
                    $valid_kurikulum_subject_data = array_diff($key_kurikulum_iuli_subject, $key_kurikulum_feeder_subject);

                    $o_curriculum->kurikulum_feeder_subject = $kurikulum_feeder_subject;
                    $o_curriculum->kurikulum_feeder = $kurikulum_feeder;
                    $o_curriculum->kurikulum_iuli_subject = $kurikulum_iuli_subject;
                    $o_curriculum->kurikulum_iuli = $kurikulum_iuli;
                    $o_curriculum->kurikulum_valid = $valid_kurikulum_data;
                    $o_curriculum->kurikulum_subject_valid = $valid_kurikulum_subject_data;
                    $o_curriculum->total_subject = $d_total_subject;
                    $o_curriculum->feeder_avail = $s_feeder_status;
                }
            }
            $mba_kurikulum_iuli = array_values($mba_kurikulum_iuli);
        }

        if ($this->input->is_ajax_request()) {
            print json_encode(['data' => $mba_kurikulum_iuli]);
        }
        else {
            return $mba_kurikulum_iuli;
        }
    }
}
