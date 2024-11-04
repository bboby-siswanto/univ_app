<?php
class Sandbox_feeder extends App_core
{
    public function __construct()
	{
		parent::__construct('academic');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('academic/Class_group_model', 'Cgm');
		$this->load->model('academic/Curriculum_model', 'Cm');
		$this->load->model('academic/Semester_model', 'Sm');
		$this->load->model('academic/Subject_model', 'Sbj');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('feeder/Feeder_model', 'FeM');
        $this->load->model('employee/Employee_model', 'EeM');
        $this->load->library('FeederAPI', ['mode' => 'production']);
		// $this->load->library('FeederAPI', ['mode' => 'sandbox']);
		
		// var_dump($this->session->userdata('environment'));
    }

    public function update_prodi_farmasi($s_id_registrasi_mahasiswa)
    {
        $kimia_study_program_id = '6ce5bc8b-10f5-456d-855d-aef18dc641f4';
        $farmasi_study_program_id = 'bfad84ea-f6f9-441e-af70-f75900b6112f';
        
        $s_dikti_semester = '20191';
        // $s_id_registrasi_mahasiswa = 'c521af7f-cb19-4c55-ad92-2caa25e6e2e8';

        $o_update_nilai_perkuliahan_kelas = $this->feederapi->post('UpdateRiwayatPendidikanMahasiswa', array(
            'record' => [
                'id_prodi' => $farmasi_study_program_id
            ],
            'key' => [
                'id_registrasi_mahasiswa' => $s_id_registrasi_mahasiswa
            ]
        ));

        print('<pre>');
        var_dump($o_update_nilai_perkuliahan_kelas);exit;
    }
    
    public function remove_kelas_kuliah()
    {
        $farmasi_study_program_id = 'bfad84ea-f6f9-441e-af70-f75900b6112f';
        $s_dikti_semester = '20191';

        $o_get_farmasi_kelas_kuliah = $this->feederapi->post('GetListKelasKuliah', [
			'filter' => "id_prodi = '{$farmasi_study_program_id}' AND id_semester = '{$s_dikti_semester}'"
        ]);

        // print('<pre>');
        // var_dump($o_get_farmasi_kelas_kuliah);exit;

        if ($o_get_farmasi_kelas_kuliah->error_code != 0) {
            print('<pre>');
            var_dump($o_get_farmasi_kelas_kuliah);exit;
        }else if (count($o_get_farmasi_kelas_kuliah->data) > 0) {
            $a_data_kelas = $o_get_farmasi_kelas_kuliah->data;

            foreach ($a_data_kelas as $o_kelas) {
                $delete_dosen_pengajar = $this->delete_dosen_pengajar($o_kelas->id_kelas_kuliah);
                if ($delete_dosen_pengajar['error_code'] != 0) {
                    print('<h1>Error dosen brooh</h1>');
                    print('<pre>');
                    var_dump($delete_dosen_pengajar);exit;
                }

                $delete_peserta_kelas_kuliah = $this->delete_peserta_kelas_kuliah($o_kelas->id_kelas_kuliah);
                if ($delete_peserta_kelas_kuliah['error_code'] != 0) {
                    print('<h1>Error peserta brooh</h1>');
                    print('<pre>');
                    var_dump($delete_peserta_kelas_kuliah);exit;
                }

                $delete_kelas = $this->feederapi->post('DeleteKelasKuliah', array(
                    'key' => array(
                        'id_kelas_kuliah' => $o_kelas->id_kelas_kuliah
                    )
                ));

                if ($delete_kelas->error_code != 0) {
                    print('<h1>Error delete kelas</h1>');
                    print('<pre>');
                    var_dump($delete_kelas);exit;
                }

                // print('<pre>');
                // var_dump($o_kelas);exit;
            }
        }else{
            print('kosyong!');
        }
        
    }

    public function delete_dosen_pengajar($s_id_kelas_kuliah)
    {
        $o_get_dosen_kelas_kuliah = $this->feederapi->post('GetDosenPengajarKelasKuliah', [
			'filter' => "id_kelas_kuliah = '{$s_id_kelas_kuliah}'"
        ]);

        if ($o_get_dosen_kelas_kuliah->error_code != 0) {
            $a_return = (array)$o_get_dosen_kelas_kuliah;
        }else if (count($o_get_dosen_kelas_kuliah->data) > 0) {
            $a_data_dosen = $o_get_dosen_kelas_kuliah->data;

            foreach ($a_data_dosen as $o_dosen_kelas) {
                $delete_dosen_pengajar = $this->feederapi->post('DeleteDosenPengajarKelasKuliah', array(
                    'key' => array(
                        'id_aktivitas_mengajar' => $o_dosen_kelas->id_aktivitas_mengajar
                    )
                ));

                if ($delete_dosen_pengajar->error_code != 0) {
                    $a_return = (array)$delete_dosen_pengajar;
                    return $a_return;
                }

            }

            $a_return = ['error_code' => 0, 'message' => 'success'];
        }else{
            $a_return = ['error_code' => 0, 'message' => 'success'];
        }

        return $a_return;
    }

    public function delete_peserta_kelas_kuliah($s_id_kelas_kuliah)
    {
        $o_get_peserta_kelas_kuliah = $this->feederapi->post('GetPesertaKelasKuliah', [
			'filter' => "id_kelas_kuliah = '{$s_id_kelas_kuliah}'"
        ]);

        if ($o_get_peserta_kelas_kuliah->error_code != 0) {
            $a_return = (array)$o_get_peserta_kelas_kuliah;
        }else if (count($o_get_peserta_kelas_kuliah->data) > 0) {
            $a_data_peserta = $o_get_peserta_kelas_kuliah->data;

            foreach ($a_data_peserta as $o_peserta_kelas) {
                $delete_peserta_kelas = $this->feederapi->post('DeletePesertaKelasKuliah', array(
                    'key' => array(
                        'id_registrasi_mahasiswa' => $o_peserta_kelas->id_registrasi_mahasiswa,
                        'id_kelas_kuliah' => $s_id_kelas_kuliah
                    )
                ));

                if ($delete_peserta_kelas->error_code != 0) {
                    $a_return = (array)$delete_peserta_kelas;
                    return $a_return;
                }

            }

            $a_return = ['error_code' => 0, 'message' => 'success'];
        }else{
            $a_return = ['error_code' => 0, 'message' => 'success'];
        }

        return $a_return;
    }
}
