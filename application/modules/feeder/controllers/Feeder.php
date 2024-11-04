<?php
class Feeder extends App_core
{
	public $s_message;
	public $number_score_process = 1;
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
		$this->s_message = '';
	}
	
	public function index()
	{
		$this->a_page_data['a_semester_type'] = $this->Sm->get_semester_type_lists(false, false, array(1, 2, 3));
		$this->a_page_data['a_academic_year'] = $this->Aym->get_academic_year_lists();
		$this->a_page_data['body'] = $this->load->view('index', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function retrieve_negara()
    {
        $a_result = $this->feederapi->post("GetNegara");
		if ($a_result->error_code == 0) {
			foreach ($a_result->data as $o_negara) {
				$country_code = $o_negara->id_negara;
				$data_negara = $this->General->get_where('ref_country', ['country_code' => $country_code]);
				if (!$data_negara) {
					$this->db->insert('ref_country', [
						'country_id' => $this->uuid->v4(),
						'country_code' => $country_code,
						'country_name' => $o_negara->nama_negara
					]);
					print('negara '.$o_negara->nama_negara.' ditambahkan<br>');
				}
				else {
					print('negara '.$o_negara->nama_negara.' sudah ada<br>');
				}
			}
		}
		else {
			print('kosong');exit;
		}
    }

	public function get_execute($s_function, $s_filter_data = false)
	{
		if ($filter_data) {
			$a_result = $this->feederapi->post($s_function, [
				'filter' => $s_filter_data
			]);
		}
		else {
			$a_result = $this->feederapi->post($s_function);
		}
		return $a_result;
	}

	public function cek_riwayat_nilai($s_student_id)
	{
		$mba_studentforlap_score = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
			'filter' => "id_registrasi_mahasiswa='$s_student_id'"
		]);

		if ($mba_studentforlap_score->error_code == 0) {
			$d_total_sks = 0;
			$d_total_merit = 0;
			$d_total_index = 0;
			print('<table border="1">');
			print('<tr><td>Periode</td><td>Mata kuliah</td><td>SKS</td><td>Nilai Angka</td><td>Nilai Huruf</td><td>Nilai Index (GP)</td><td>Nilai Merit</td></tr>');
			foreach ($mba_studentforlap_score->data as $key => $value) {
				$nilai_angka = $value->nilai_angka;
				$sks_mata_kuliah = $value->sks_mata_kuliah;
				$s_nilai_index = $value->nilai_indeks;
				$d_gradepoint = $this->grades->get_grade_point($nilai_angka);
				$d_merit = $this->grades->get_merit($sks_mata_kuliah, $d_gradepoint);

				$d_total_sks += $sks_mata_kuliah;
				$d_total_merit += $d_merit;
				// $d_total_index += $s_nilai_index;
				
				print('<tr><td>'.$value->id_periode.'</td><td>'.$value->nama_mata_kuliah.'</td><td>'.$sks_mata_kuliah.'</td><td>'.$nilai_angka.'</td><td>'.$value->nilai_huruf.'</td><td>'.$value->nilai_indeks.'</td><td>'.$d_merit.'</td></tr>');
				// exit;
				// print('<pre>');var_dump($mba_studentforlap_detail);exit;
				// print('<br>');
			}

			$d_ipkmerit = $this->grades->get_ipk($d_total_merit, $d_total_sks);

			print('<tr><td colspan="2">Total</td><td>'.$d_total_sks.'</td><td colspan="3"></td><td>'.$d_total_merit.'</td></tr>');
			print('<tr><td colspan="2">IPK</td><td colspan="5">'.$d_ipkmerit.'</td></tr>');
			print('</table>');
		}

		// print('<pre>');var_dump($mba_studentforlap_score);exit;
	}

	function get_student_detail() {
		$s_student_id_portal = 'fb998458-50fd-443e-9c58-3f44509adf24';
		$mba_student_data =$this->General->get_where('dt_student', ['student_id' => $s_student_id_portal]);
		if ($mba_student_data) {
			$mba_student_forlap = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', [
				'filter' => "nim='".$mba_student_data[0]->student_number."'"
			]);

			print('<pre>');var_dump($mba_student_forlap);exit;
		}
		else {
			print('mahasiswa tidak ditemukan diportal');exit;
		}
	}

	function get_mahasiswa_keluar() {
		$mba_list_student_feeder = $this->feederapi->post('GetListMahasiswaLulusDO', [
			'filter' => "id_jenis_keluar IN ('3','4')"
		]);
		print('<pre>');var_dump($mba_list_student_feeder);exit;
	}

	public function get_student_graduate_ipk()
	{
		$mba_studentforlap_graduate = $this->feederapi->post('GetListMahasiswaLulusDO', [
			'filter' => "id_jenis_keluar='1'"
		]);

		if ($mba_studentforlap_graduate->error_code == 0) {
			print('<table border="1">');
			print('<tr><td>Nama</td><td>student_id</td><td>Prodi</td><td>NIM</td><td>IPK</td><td>Tanggal SK Yudisium</td><td>Periode Keluar</td></tr>');
			foreach ($mba_studentforlap_graduate->data as $key => $value) {
				$s_id_regmahasiswa = $value->id_registrasi_mahasiswa;
				$mba_studentforlap_detail = $this->feederapi->post('GetDetailMahasiswaLulusDO', [
					'filter' => "id_registrasi_mahasiswa='$s_id_regmahasiswa'"
				]);

				if ($mba_studentforlap_detail->error_code == 0) {
					$o_data = $mba_studentforlap_detail->data[0];
					print('<tr><td>'.$o_data->nama_mahasiswa.'</td><td>'.$o_data->id_registrasi_mahasiswa.'</td><td>'.$o_data->nama_program_studi.'</td><td>'.$o_data->nim.'</td><td>'.$o_data->ipk.'</td><td>'.$o_data->tanggal_sk_yudisium.'</td><td>'.$o_data->id_periode_keluar.'</td></tr>');
				}
				else {
					print('<tr><td>'.$value->nama_mahasiswa.'</td><td>'.$value->id_registrasi_mahasiswa.'</td><td>'.$value->nama_program_studi.'</td><td>'.$o_data->nim.'</td><td></td><td></td><td></td></tr>');
				}
				// print('<pre>');var_dump($mba_studentforlap_detail);exit;
				// print('<br>');
			}
			print('</table>');
		}
		// print('<pre>');var_dump($mba_studentforlap_graduate);exit;
	}
	
	public function execute($s_function, $b_beautify_mode = false)
	{
		$a_result = $this->feederapi->post($s_function);
		if ($b_beautify_mode) {
			if ($a_result->error_code == 0) {
				if ((is_array($a_result->data)) AND (count($a_result->data) > 0)) {
					$datalist = $a_result->data;
					print('<table border="1" style="width:100%">');
					print('<tr>');
					foreach ($datalist[0] as $key => $valuekey) {
						print('<td>'.$key.'</td>');
					}
					print('</tr>');
					foreach ($datalist as $result) {
						print('<tr>');
						foreach ($datalist[0] as $key => $valuekey) {
							print('<td>="'.$result->$key.'"</td>');
						}
						print('</tr>');
					}
					print('</table>');
				}
				else {
					print "<pre>";
					var_dump($a_result);exit;
				}
			}
			else {
				print "<pre>";
				var_dump($a_result);exit;
			}
		}
		else {
			print "<pre>";
			var_dump($a_result);exit;
		}
	}

	public function get_data_dosen()
	{
		$a_result = $this->feederapi->post('GetListDosen');
		$a_list_dosen = $a_result->data;
		print('<table style="border:1">');
		print('<tr>');
		print('<td>nama_dosen</td>');
		print('<td>nidn</td>');
		// print('<td>tahun_ajaran</td>');
		print('<td>prodi</td>');
		print('<td>pendidikan_terakhir</td>');
		print('<td>gelar</td>');
		print('<td>asal_pendidikan</td>');
		print('</tr>');
		foreach ($a_list_dosen as $o_dosen) {
			// print('<pre>');var_dump($o_dosen);exit;
			$a_data_penugasan = $this->feederapi->post('GetListPenugasanDosen', [
				'filter' => "id_dosen='$o_dosen->id_dosen'"
			]);
			$a_data_pendidikan = $this->feederapi->post('GetRiwayatPendidikanDosen', [
				'filter' => "id_dosen='$o_dosen->id_dosen'"
			]);
			$a_penugasan_data = $a_data_penugasan->data;
			$a_pendidikan_data = $a_data_pendidikan->data;
			$s_prodi = '';
			$s_batch = '';
			$s_lulusan = '';
			$s_lulusan_gelar = '';
			$s_lulusan_asal = '';
			if (count($a_penugasan_data) > 0) {
				$s_prodi = $a_penugasan_data[count($a_penugasan_data) - 1]->nama_program_studi;
				$s_batch = $a_penugasan_data[count($a_penugasan_data) - 1]->nama_tahun_ajaran;
			}

			if (count($a_pendidikan_data) > 0) {
				$s_lulusan = $a_pendidikan_data[count($a_pendidikan_data) - 1]->nama_jenjang_pendidikan;
				$s_lulusan_gelar = $a_pendidikan_data[count($a_pendidikan_data) - 1]->nama_gelar_akademik;
				$s_lulusan_asal = $a_pendidikan_data[count($a_pendidikan_data) - 1]->nama_perguruan_tinggi;
			}
			
			$a_data = [
				'nama' => $o_dosen->nama_dosen,
				'nidn' => '="'.$o_dosen->nidn.'"',
				// 'tahun_ajaran' => $s_batch,
				'prodi' => $s_prodi,
				'pendidikan_terakhir' => $s_lulusan,
				'gelar' => $s_lulusan_gelar,
				'asal_pendidikan' => $s_lulusan_asal,
			];

			print('<tr>');
			print('<td>'.$o_dosen->nama_dosen.'</td>');
			print('<td>="'.$o_dosen->nidn.'"</td>');
			// print('<td>'.$s_batch.'</td>');
			print('<td>'.$s_prodi.'</td>');
			print('<td>'.$s_lulusan.'</td>');
			print('<td>'.$s_lulusan_gelar.'</td>');
			print('<td>'.$s_lulusan_asal.'</td>');
			print('</tr>');
		}
		print('</table>');
		// print('<pre>');var_dump($a_result);exit;
	}

	public function GetKRSMahasiswa($s_student_id)
	{
		// $a_result = $this->feederapi->post('GetKRSMahasiswa', [
		// 	'filter' => "id_registrasi_mahasiswa='$s_student_id' AND id_periode = '20211'"
		// ]);

		$a_result = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
			'filter' => "id_registrasi_mahasiswa='$s_student_id' AND id_periode='20211'"
		]);

		print('<pre>');
		var_dump($a_result);exit;
	}

	public function nilai_transfer($s_student_id)
	{
		$a_result = $this->feederapi->post('GetNilaiTransferPendidikanMahasiswa', [
			'filter' => "id_registrasi_mahasiswa='$s_student_id'"
		]);

		if ($a_result->error_code == 0) {
			$i_total_sks = 0;
			print('<table border="1">');
			print('<tr><th>Nama Mata Kuliah</th><th>SKS</th></tr>');

			foreach ($a_result->data as $key => $value) {
				print('<tr>');
				print('<td>'.$value->nama_mata_kuliah_diakui.'</td>');
				print('<td>'.$value->sks_mata_kuliah_diakui.'</td>');
				print('</tr>');

				$i_total_sks += $value->sks_mata_kuliah_diakui;
			}

			print('</table>');
			print('<h2>Total SKS Transfer = <b>'.$i_total_sks.'</b></h2>');
		}
		else {
			print('<pre>');var_dump($a_result);
		}
	}

	public function get_krs_siswa()
	{
		// $a_score_forlap = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
		// 	'filter' => "id_registrasi_mahasiswa='2ce485fd-b21c-4504-8d8e-d8220a67ba36' AND id_prodi='12c9ec75-af4a-46a1-ae12-b1ba4bf75c89' AND id_periode='20202'"
		// ]);

		$a_score_forlap = $this->feederapi->post('GetPesertaKelasKuliah', [
			'filter' => "id_kelas_kuliah='d15d6653-d28c-4111-8fd8-b5550a7dc25b'"
		]);

		print('<pre>');
		var_dump($a_score_forlap);exit;
	}
	
	public function compare_score_semester($s_student_id)
	{
		$mbo_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id])[0];
		$mbo_prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mbo_student_data->study_program_id])[0];
		$s_study_program_id = (!is_null($mbo_prodi_data->study_program_main_id)) ? $mbo_prodi_data->study_program_main_id : $mbo_prodi_data->study_program_id;
		$a_result = $this->feederapi->post('GetDetailPerkuliahanMahasiswa', [
			'filter' => "id_registrasi_mahasiswa='$s_student_id'",
			'order' => "id_semester ASC"
		]);

		if ($a_result->error_code == 0) {
			$i_total_sks_portal = 0;
			$i_total_sks_forlap = 0;
			$i_total_merit_portal = 0;
			$i_total_merit_forlap = 0;

			print('<h2>'.$mbo_student_data->student_email.'</h2>');
			print('<table border="1">');
			print('<tr><th>Semester</th><th>Jumlah SKS Portal</th><th>IPS Portal</th><th>Jumlah SKS Forlap</th><th>IPS Forlap</th><th>IPK Portal</th><th>IPK Forlap</th></tr>');

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

				$a_score_forlap = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
					'filter' => "id_registrasi_mahasiswa='$s_student_id' AND id_periode='$o_semester->id_semester'"
				]);

				$i_sks_portal = 0;
				$i_merit_portal = 0;
				if ($mba_score_data) {
					foreach ($mba_score_data as $o_score) {
						$i_sks_portal += $o_score->curriculum_subject_credit;
						$i_grade_point = $this->grades->get_grade_point($o_score->score_sum);
						$i_merit_portal += $this->grades->get_merit($o_score->curriculum_subject_credit, $i_grade_point);
					}
				}
				
				$i_sks_forlap = 0;
				$i_merit_forlap = 0;
				$b_german_found = false;
				if ($a_score_forlap->error_code == 0) {
					foreach ($a_score_forlap->data as $o_score) {
						// print($o_score->nama_mata_kuliah);
						$pos = strpos($o_score->nama_mata_kuliah, 'German');
						if ($pos !== false) {
							$b_german_found = true;
						}
						$i_sks_forlap += $o_score->sks_mata_kuliah;
						$i_merit_forlap += $o_score->sks_mata_kuliah * $o_score->nilai_indeks;
					}
				}else{
					print('<pre>');
					var_dump($a_score_forlap);exit;
				}

				$s_ips_portal = $this->grades->get_ipk($i_merit_portal, $i_sks_portal);
				$s_ips_forlap = $this->grades->get_ipk($i_merit_forlap, $i_sks_forlap);

				$i_total_sks_portal += $i_sks_portal;
				$i_total_sks_forlap += $i_sks_forlap;
				$i_total_merit_portal += $i_merit_portal;
				$i_total_merit_forlap += $i_merit_forlap;

				$s_current_ipk_portal = $this->grades->get_ipk($i_total_merit_portal, $i_total_sks_portal);
				$s_current_ipk_forlap = $this->grades->get_ipk($i_total_merit_forlap, $i_total_sks_forlap);

				print('<tr>');
				print('<td>'.$o_semester->id_semester.'</td>');
				print('<td>'.$i_sks_portal.'</td>');
				print('<td>'.$s_ips_portal.'</td>');
				print('<td>'.$i_sks_forlap.' '.(($b_german_found) ? '+german' : '').'</td>');
				print('<td>'.$s_ips_forlap.'</td>');
				print('<td>'.$s_current_ipk_portal.'</td>');
				print('<td>'.$s_current_ipk_forlap.'</td>');
				print('</tr>');
			}

			$s_ipk_portal = $this->grades->get_ipk($i_total_merit_portal, $i_total_sks_portal);
			$s_ipk_forlap = $this->grades->get_ipk($i_total_merit_forlap, $i_total_sks_forlap);
			print('</table>');
			print('<h2>Total SKS Portal = <b>'.$i_total_sks_portal.'</b></h2>');
			print('<h2>IPK Portal = <b>'.$s_ipk_portal.'</b></h2>');
			print('<h2>Total SKS Forlap = <b>'.$i_total_sks_forlap.'</b></h2>');
			print('<h2>IPK Forlap = <b>'.$s_ipk_forlap.'</b></h2>');
		}
		else{
			print('<pre>');
			var_dump($a_result);
		}
	}

	public function get_data_mahasiswa($s_student_id)
	{
		$mbo_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id])[0];
		$s_personal_data_id = $mbo_student_data->personal_data_id;
		$a_score_forlap = $this->feederapi->post('GetListMahasiswa', [
			'filter' => "id_registrasi_mahasiswa='$s_student_id' OR id_mahasiswa='$s_personal_data_id'"
		]);
		print('<h3>Forlap</h3>');
		print('<pre>');var_dump($a_score_forlap);
		print('<h3>portal</h3>');
		print('<pre>');var_dump($mbo_student_data);
	}

	public function GetRiwayatNilaiMahasiswaPortal($s_student_id, $s_academic_year_id = false, $s_semester_type_id = false)
	{
		$a_param_score = array(
			'sc.student_id' => $s_student_id,
			'sc.score_approval' => 'approved',
			'sc.score_display' => 'TRUE',
			'sc.semester_id !=' => '17',
			'curriculum_subject_credit !=' => '0',
			'curriculum_subject_type !=' => 'extracurricular'
		);

		if ($s_academic_year_id AND $s_semester_type_id) {
			$a_param_score['sc.academic_year_id'] = $s_academic_year_id;
			$a_param_score['sc.semester_type_id'] = $s_semester_type_id;
		}

		// $o_semester_active = $this->Smm->get_active_semester();
		$mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2,3,7,8]);
		if ($mba_score_data) {
			$i_total_sks = 0;
			$i_total_merit = 0;
			print('<table border="1">');
			print('<tr><th>Nama Mata Kuliah</th><th>id_kelas</th><th>Periode</th><th>SKS</th><th>Nilai</th><th>Grade</th><th>Merit</th></tr>');
			foreach ($mba_score_data as $o_prodi) {
				$i_total_sks += $o_prodi->curriculum_subject_credit;
				$s_grade_poin = $this->grades->get_grade_point($o_prodi->score_sum);
				$s_grade = $this->grades->get_grade($o_prodi->score_sum);
				$i_merit = $this->grades->get_merit($o_prodi->curriculum_subject_credit, $s_grade_poin);
				$i_total_merit += $i_merit;

				print('<tr>');
				print('<td>'.$o_prodi->subject_name.'</td>');
				print('<td>'.$o_prodi->class_group_id.'</td>');
				print('<td>'.$o_prodi->academic_year_id.$o_prodi->semester_type_id.'</td>');
				print('<td>'.str_replace('.', ',', $o_prodi->curriculum_subject_credit).'</td>');
				print('<td>'.str_replace('.', ',', $o_prodi->score_sum).'</td>');
				print('<td>'.$s_grade.'</td>');
				print('<td>'.number_format($i_merit, 2, ",", ",").'</td>');
				print('</tr>');
			}
			$ipk = $this->grades->get_ipk($i_total_merit, $i_total_sks);
			print('</table>');
			print('<h2>Total SKS = <b>'.$i_total_sks.'</b></h2>');
			print('<h2>IPK = <b>'.$ipk.'</b></h2>');
		}
	}

	public function GetRiwayatNilaiMahasiswa($s_student_id, $s_semester_dikti = false)
	{
		$mbo_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id])[0];
		$mbo_prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mbo_student_data->study_program_id])[0];

		$s_study_program_id = (!is_null($mbo_prodi_data->study_program_main_id)) ? $mbo_prodi_data->study_program_main_id : $mbo_prodi_data->study_program_id;
		$s_filter = "id_registrasi_mahasiswa='$s_student_id' AND id_prodi='$s_study_program_id'";

		if ($s_semester_dikti) {
			$s_filter .= " AND id_periode='$s_semester_dikti'";
		}
		$a_result = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
			'filter' => "$s_filter",
			'order' => "nama_mata_kuliah ASC"
		]);

		// print('<pre>');var_dump($a_result);exit;

		print('<h3>'.$mbo_student_data->student_email.'</h3>');

		if ($a_result->error_code == 0) {
			$i_total_sks = 0;
			$i_total_merit = 0;
			print('<table border="1">');
			print('<tr><th>Nama Mata Kuliah</th><th>id_kelas</th><th>Periode</th><th>SKS</th><th>Nilai</th><th>Grade</th><th>Merit</th></tr>');
			foreach ($a_result->data as $o_prodi) {
				$i_total_sks += $o_prodi->sks_mata_kuliah;
				$i_merit = $o_prodi->sks_mata_kuliah * $o_prodi->nilai_indeks;
				$i_total_merit += $i_merit;

				print('<tr>');
				print('<td>'.$o_prodi->nama_mata_kuliah.'</td>');
				print('<td>'.$o_prodi->id_kelas.'</td>');
				print('<td>'.$o_prodi->id_periode.'</td>');
				print('<td>'.str_replace('.', ',', $o_prodi->sks_mata_kuliah).'</td>');
				print('<td>'.str_replace('.', ',', $o_prodi->nilai_angka).'</td>');
				print('<td>'.$o_prodi->nilai_huruf.'</td>');
				print('<td>'.$i_merit.'</td>');
				print('</tr>');
			}
			$ipk = $this->grades->get_ipk($i_total_merit, $i_total_sks);
			print('</table>');
			print('<h2>Total SKS = <b>'.$i_total_sks.'</b></h2>');
			print('<h2>IPK = <b>'.$ipk.'</b></h2>');
		}else{
			print('<pre>');var_dump($a_result);exit;
		}
	}

	public function DeletePesertaKelasKuliah($s_id_kelas, $s_student_id = false)
	{
		$a_result = $this->feederapi->post('GetPesertaKelasKuliah', [
			'filter' => "id_kelas_kuliah='$s_id_kelas'"
		]);

		if ($s_student_id) {
			// $a_result = $this->feederapi->post('GetPesertaKelasKuliah', [
			// 	'filter' => "id_kelas_kuliah='$s_id_kelas' AND id_registrasi_mahasiswa='$s_student_id'"
			// ]);

			$a_result = $this->feederapi->post('DeletePesertaKelasKuliah', [
				'key' => array(
					'id_kelas_kuliah' => $s_id_kelas,
					'id_registrasi_mahasiswa' => $s_student_id
				)
			]);
		}

		// if ($a_result->error_code == 0) {
		// 	foreach ($a_result->data as $o_data) {
		// 		if ($o_data->id_registrasi_mahasiswa != '900d032d-5f3d-46ac-a21c-6ae1d23c8e3a') {
		// 			$result_delete = $this->feederapi->post('DeletePesertaKelasKuliah', [
		// 				'key' => array(
		// 					'id_kelas_kuliah' => $s_id_kelas,
		// 					'id_registrasi_mahasiswa' => $o_data->id_registrasi_mahasiswa
		// 				)
		// 			]);
	
		// 			if ($result_delete->error_code == 0) {
		// 				print('delete '.$o_data->nama_mahasiswa.'<br>');
		// 			}else{
		// 				print('<pre>');
		// 				var_dump($result_delete);exit;
		// 			}
		// 		}
		// 	}
		// }else{
			print('<pre>');
			var_dump($a_result);
		// }
	}

	public function get_prodi_pt($kode_perguruantinggi)
	{
		$a_result = $this->feederapi->post('GetAllProdi', [
			'filter' => "kode_perguruan_tinggi='$kode_perguruantinggi'"
		]);

		// print('<pre>');
		// var_dump($a_result->data);

		print('<table border="1">');
		print('<tr><th>Kode Prodi</th><th>Nama Prodi</th><th>ID Prodi</th></tr>');
		foreach ($a_result->data as $o_prodi) {
			print('<tr>');
			print('<td>'.$o_prodi->kode_program_studi.'</td>');
			print('<td>'.$o_prodi->nama_program_studi.'</td>');
			print('<td>'.$o_prodi->id_prodi.'</td>');
			print('</tr>');
		}
		print('</table>');
	}
	public function get_prodi_iuli()
	{
		$a_result = $this->feederapi->post('GetAllProdi', [
			'filter' => "kode_perguruan_tinggi='041058'"
		]);

		// print('<pre>');
		// var_dump($a_result->data);

		print('<table border="1">');
		print('<tr><th>Kode Prodi</th><th>Nama Prodi</th><th>ID Prodi</th></tr>');
		foreach ($a_result->data as $o_prodi) {
			print('<tr>');
			print('<td>'.$o_prodi->kode_program_studi.'</td>');
			print('<td>'.$o_prodi->nama_program_studi.'</td>');
			print('<td>'.$o_prodi->id_prodi.'</td>');
			print('</tr>');
		}
		print('</table>');
	}

	public function sync_dikti_to_portal($s_function_dikti, $s_portal_table)
	{
		$a_dikti_data = $this->feederapi->post($s_function_dikti);
		$a_prep_data = array();

		if ($a_dikti_data->error_code == 0) {
			foreach ($a_dikti_data->data as $o_dikti_data) {
				$a_dikti_data = (array)$o_dikti_data;
				
				$mba_checker_data = $this->FeM->get_where($s_portal_table, [array_keys($a_dikti_data)[0] => array_values($a_dikti_data)[0]]);

				foreach ($o_dikti_data as $key => $value) {
					$o_dikti_data->{$key} = trim($value);
				}
				// array_push($a_prep_data, (array)$o_dikti_data);
				
				switch ($s_portal_table) {
					case 'dikti_kategori_kegiatan':
						if ($mba_checker_data) {
							$save_data = $this->FeM->save_dikti_kategori_kegiatan($a_dikti_data, [array_keys($a_dikti_data)[0] => array_values($a_dikti_data)[0]]);
						}else{
							$save_data = $this->FeM->save_dikti_kategori_kegiatan($a_dikti_data);
						}

						break;

					case 'dikti_jenis_aktivitas':
						unset($a_dikti_data['untuk_kampus_merdeka']);
						if ($mba_checker_data) {
							$save_data = $this->FeM->save_dikti_jenis_aktivitas($a_dikti_data, [array_keys($a_dikti_data)[0] => array_values($a_dikti_data)[0]]);
						}else{
							$save_data = $this->FeM->save_dikti_jenis_aktivitas($a_dikti_data);
						}

						break;
					
					default:
						$save_data = false;
						break;
				}

				if ($save_data) {
					$this->iuli_lib->print_pre($a_dikti_data);
				}else{
					$this->iuli_lib->print_pre('Error insert!');;
				}
			}
		}else{
			$this->iuli_lib->print_pre($a_dikti_data);
		}

	}

	public function sync_prodi_semester($s_academic_year_id, $s_semester_type_id)
	{
		// InsertPeriodePerkuliahan
		$mba_semester_data = $this->Sm->get_semester_setting([
			'dss.academic_year_id' => $s_academic_year_id,
			'dss.semester_type_id' => $s_semester_type_id
		]);

		if ($mba_semester_data) {
			$o_semester_data = $mba_semester_data[0];
			$mba_prodi_data = $this->General->get_where('ref_study_program', [
				'study_program_main_id' => NULL,
				'dikti_code != ' => NULL,
				'study_program_is_active' => 'yes'
			]);

			if ($mba_prodi_data) {
				foreach ($mba_prodi_data as $o_prodi) {
					$o_get_period_perkuliahan_prodi = $this->feederapi->post('GetCountPeriodePerkuliahan', [
						'filter' => "id_prodi = '$o_prodi->study_program_id' AND id_semester = '$s_academic_year_id.$s_semester_type_id'"
					]);

					if ($o_get_period_perkuliahan_prodi->data == 0) {
						$a_data = [
							"id_prodi" =>  $o_prodi->study_program_id,
							"id_semester" =>  $s_academic_year_id.$s_semester_type_id,
							"jumlah_target_mahasiswa_baru" =>  "0",
							"jumlah_pendaftar_ikut_seleksi" =>  "0",
							"jumlah_pendaftar_lulus_seleksi" =>  "0",
							"jumlah_daftar_ulang" =>  "0",
							"jumlah_mengundurkan_diri" =>  "0",
							"jumlah_minggu_pertemuan" =>  (in_array($s_semester_type_id, [1,2])) ? "16" : "16",
							"tanggal_awal_perkuliahan" =>  date('Y-m-d', strtotime($o_semester_data->semester_start_date)),
							"tanggal_akhir_perkuliahan" =>  date('Y-m-d', strtotime($o_semester_data->semester_end_date))
						];
	
						$o_insert_period_perkuliahan_prodi = $this->feederapi->post('InsertPeriodePerkuliahan', [
							'record' => $a_data
						]);
	
						if ($o_insert_mata_kuliah->error_code == 0) {
							print('Prodi '.$o_prodi->study_program_name_feeder.' ditambahkan<br>');
						}
						else {
							print('<pre>');var_dump($o_insert_mata_kuliah);exit;
						}
					}
					else {
						print('Prodi '.$o_prodi->study_program_name_feeder.' sudah ada<br>');
					}
				}
			}
			else {
				print('Prodi not found!');exit;
			}
		}
		else {
			print('Semester selected not found!');exit;
		}
	}
	
	public function sync_wilayah_dikti()
	{
		$a_wilayah_dikti = $this->feederapi->post('GetWilayah');
		$a_db_wilayah = [];
		foreach($a_wilayah_dikti->data as $o_wilayah){
			$mbo_check_dikti_wilayah = $this->FeM->check_dikti_wilayah($o_wilayah->id_wilayah);
			if(!$mbo_check_dikti_wilayah){
				foreach($o_wilayah as $key => $value){
					$o_wilayah->{$key} = trim($value);
				}
				array_push($a_db_wilayah, (array)$o_wilayah);
			}
		}
		
		if(count($a_db_wilayah) >= 1){
			$this->FeM->insert_dikti_wilayah_batch($a_db_wilayah);
		}
	}
	
	public function get_detail_matkul($s_id_matkul)
	{
		$o_get_detail_mata_kuliah = $this->feederapi->post('GetDetailMataKuliah', [
			'filter' => "id_matkul = '{$s_id_matkul}'"
		]);
		if(($o_get_detail_mata_kuliah->error_code == 0) AND (count($o_get_detail_mata_kuliah->data) != 0)){
			return true;
		}
		return false;
	}

	public function test()
	{
		$s_class_group_id = '9f4f680d-7caa-420b-ad52-26dd89991db5';

		$mba_class_groups = $this->Cgm->get_class_group_filtered([
			'cmc.class_group_id' => $s_class_group_id
		], true)[0];

		print('<pre>');
		$aaa = $this->_check_matkul_feeder($mba_class_groups);
		var_dump($mba_class_groups);
	}

	private function _check_matkul_feeder($o_subject_data)
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
				
				$a_data_mata_kuliah = [
					'id_prodi' => (is_null($o_subject_data->subject_study_program_main_id)) ? $o_subject_data->subject_study_program_id : $o_subject_data->subject_study_program_main_id,
					'kode_mata_kuliah' => $o_subject_data->subject_code,
					'nama_mata_kuliah' => $o_subject_data->subject_name,
					'id_jenis_mata_kuliah' => $s_id_jenis_matakuliah,
					'sks_mata_kuliah' => $o_subject_data->curriculum_subject_credit,
					'sks_tatap_muka' => $o_subject_data->curriculum_subject_credit,
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
					
					// if(!$this->Sbj->get_subject_filtered([
					// 	'subject_id' => $s_id_matkul
					// ])){
					// 	$this->Sbj->save_subject_data([
					// 		'subject_id' => $s_id_matkul
					// 	], $o_subject_data->subject_id);
					// }
					return $s_id_matkul;
				}
				else{
					$a_get_detail_mata_kuliah = $this->feederapi->post('GetDetailMataKuliah', [
						'filter' => "kode_mata_kuliah = '{$o_subject_data->subject_code}' AND nama_mata_kuliah = '{$o_subject_data->subject_name}'"
					]);
					
					if($a_get_detail_mata_kuliah->error_code == 0 && count($a_get_detail_mata_kuliah->data) >= 1){
						$s_id_matkul = $a_get_detail_mata_kuliah->data[0]->id_matkul;
						// $this->Sbj->save_subject_data([
						// 	'subject_id' => $s_id_matkul
						// ], $o_subject_data->subject_id);
						return $s_id_matkul;
					}else{
						print('004<br>');
						var_dump($a_get_detail_mata_kuliah);
						var_dump($o_insert_mata_kuliah);
					}
				}
			}
		}
	}

	public function sync_student_krs($s_student_id, $s_academic_year_id, $s_semester_type_id)
	{
		$mba_score_data = $this->Scm->get_score_data([
			'sc.academic_year_id' => $s_academic_year_id,
			'sc.semester_type_id' => $s_semester_type_id,
			'sc.student_id' => $s_student_id,
			'sc.score_approval' => 'approved',
			'curs.curriculum_subject_type != ' => 'extracurricular'
		]);

		if ($mba_score_data) {
			$s_semester_dikti = $s_academic_year_id.$s_semester_type_id;
			print('<pre>');
			
			foreach ($mba_score_data as $o_score) {
				$a_get_kelas = $this->feederapi->post('GetDetailKelasKuliah', array(
					'filter' => "id_kelas_kuliah = '$o_score->class_group_id'"
				));

				if (($a_get_kelas->error_code == 0) AND (count($a_get_kelas->data) > 0)) {
					$a_feeder_data = $a_get_kelas->data;

					$a_peserta_kelas = $this->feederapi->post('GetPesertaKelasKuliah', array(
						'filter' => "id_kelas_kuliah = '$o_score->class_group_id' AND id_registrasi_mahasiswa = '$s_student_id'"
					));

					if (($a_peserta_kelas->error_code == 0) AND (count($a_peserta_kelas->data) == 0)) {
						$a_prep_krs = array(
							'id_kelas_kuliah' => $o_score->class_group_id,
							'id_registrasi_mahasiswa' => $s_student_id
						);
	
						$o_insert_peserta_kelas_kuliah = $this->feederapi->post('InsertPesertaKelasKuliah', array(
							'record' => $a_prep_krs
						));

						if($o_insert_peserta_kelas_kuliah->error_code == 0){
							$score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
							$grade_point = $this->grades->get_grade_point($score_sum);
							$grade = $this->grades->get_grade($score_sum);
							
							$a_prep_nilai_perkuliahan = [
								'nilai_angka' => $score_sum,
								'nilai_indeks' => $grade_point,
								'nilai_huruf' => $grade
							];
							
							$o_update_nilai_perkuliahan_kelas = $this->feederapi->post('UpdateNilaiPerkuliahanKelas', array(
								'record' => $a_prep_nilai_perkuliahan,
								'key' => $a_prep_krs
							));
							
							if($o_update_nilai_perkuliahan_kelas->error_code != 0){
								var_dump($o_update_nilai_perkuliahan_kelas);exit;
							}
						}else{
							var_dump($o_insert_peserta_kelas_kuliah);exit;
						}
					}else{
						var_dump($a_peserta_kelas);
					}

					print('Processing : '.$o_score->class_group_id.' - '.$o_score->subject_name);
					print('<br>');
				}else{
					print('kelas '.$o_score->subject_name.' belum ada! : '.$o_score->score_id.'<br>');
				}
			}
		}
	}

	public function get_detail_mahasiswa()
	{
		$o_detail_data = $this->feederapi->post('GetAktivitasKuliahMahasiswa', array(
			'filter' => "id_registrasi_mahasiswa = '9efca642-14c2-4880-9ef9-4b46eee9efc0'"
		));
		print('<pre>');var_dump($o_detail_data);
	}
	
	public function start_sync_score($s_academic_year_id, $s_semester_type_id, $s_subject_id, $s_id_kelas_kuliah, $b_all_sync = false)
	{
		// $mba_kelas_kuliah = $this->General->get_where('ref_study_program', ['study_program_id' => $s_id_kelas_kuliah]);
		// if ($mba_kelas_kuliah) {
		// 	$s_id_kelas_kuliah = (!is_null($mba_kelas_kuliah[0]->study_program_main_id));
		// }
		$a_score_param = [
			'sc.academic_year_id' => $s_academic_year_id,
			'sc.semester_type_id' => $s_semester_type_id,
			// 'sb.subject_id' => $s_subject_id,
			'sc.class_group_id' => $s_id_kelas_kuliah
		];
		$mba_score_data = $this->Scm->get_score_data($a_score_param);
		
		// if (in_array($mba_score_data[0]->student_study_program_id, ['417bc155-81cd-11e9-bdfc-5254005d90f6', '46675bdb-83af-47e7-bef6-07566108fd21'])) {
			// print("<pre>");var_dump($mba_score_data);exit;
		// }
		if($mba_score_data){
			// 
			$s_semester_dikti = $s_academic_year_id.$s_semester_type_id;
			if (in_array($s_semester_type_id, ['7', '8'])) {
				$s_semester_dikti = $s_academic_year_id.'3';
			}

			$a_riwayat_nilai_mahasiswa = $this->feederapi->post('GetPesertaKelasKuliah', array(
				'filter' => "id_kelas_kuliah = '$s_id_kelas_kuliah'"
			));

			// 
			if (($a_riwayat_nilai_mahasiswa->error_code == 0) AND (count($a_riwayat_nilai_mahasiswa->data) > 0)) {
			    $a_feeder_data = $a_riwayat_nilai_mahasiswa->data;
			    foreach ($a_feeder_data as $o_riwayat_nilai) {
			        $remove_student_kelas = $this->feederapi->post('DeletePesertaKelasKuliah', array(
			            'key' => array(
			                'id_kelas_kuliah' => "$s_id_kelas_kuliah",
			                'id_registrasi_mahasiswa' => "$o_riwayat_nilai->id_registrasi_mahasiswa"
			            )
					));
					
					$s_message = 'Deleted '.$s_id_kelas_kuliah.'-'.$o_riwayat_nilai->nama_mahasiswa.'<br>';
					if ($b_all_sync) {
						$this->s_message .= $s_message;
					}
					else {
						print($s_message);
					}
			    }
			}
			// else{
			// 	print('<pre>error get riwayat nilai:');var_dump($a_riwayat_nilai_mahasiswa);exit;
			// }

			foreach($mba_score_data as $o_score_data){
				$mbo_student_data = $this->Stm->get_student_by_id($o_score_data->student_id);
				$a_prep_krs = array(
					'id_kelas_kuliah' => "$s_id_kelas_kuliah",
					'id_registrasi_mahasiswa' => "$o_score_data->student_id"
				);
				
				if(($o_score_data->score_approval == 'approved') && (in_array($o_score_data->curriculum_subject_type, ['mandatory', 'elective']))){
					$b_update_score = false;
				
					$o_insert_peserta_kelas_kuliah = $this->feederapi->post('InsertPesertaKelasKuliah', array(
						'record' => [
							'id_kelas_kuliah' => "$s_id_kelas_kuliah",
							'id_registrasi_mahasiswa' => "$o_score_data->student_id"
						]
					));

					$s_message = $this->number_score_process++.'Processing : '.$s_id_kelas_kuliah.' - '.$mbo_student_data->personal_data_name.' ('.$mbo_student_data->study_program_abbreviation.'/'.$mbo_student_data->finance_year_id.')'.'('.$mbo_student_data->study_program_abbreviation.' - '.$o_score_data->class_group_id.' - '.$o_score_data->student_id.')'.$o_score_data->score_approval.'<br>';
					if ($b_all_sync) {
						$this->s_message .= $s_message;
					}
					else {
						print($s_message);
					}
					
					if($o_insert_peserta_kelas_kuliah->error_code == 0){
						// print('oke - ');var_dump($o_insert_peserta_kelas_kuliah);
						$b_update_score = true;
					}else{
						print('010 - ');var_dump($o_insert_peserta_kelas_kuliah);
						print('<br>');
						// exit;
					}

					if($b_update_score){
						$score_sum = round($o_score_data->score_sum, 2, PHP_ROUND_HALF_UP);
						$grade_point = round($this->grades->get_grade_point($score_sum), 2, PHP_ROUND_HALF_UP);
						$grade = $this->grades->get_grade($score_sum);
						
						$a_prep_nilai_perkuliahan = [
							'nilai_angka' => "$score_sum",
							'nilai_indeks' => "$grade_point",
							'nilai_huruf' => "$grade"
						];
						
						$o_update_nilai_perkuliahan_kelas = $this->feederapi->post('UpdateNilaiPerkuliahanKelas', array(
							'record' => $a_prep_nilai_perkuliahan,
							'key' => $a_prep_krs
						));
						
						if($o_update_nilai_perkuliahan_kelas->error_code != 0){
							var_dump($o_update_nilai_perkuliahan_kelas);exit;
						}
						// print_r($o_update_nilai_perkuliahan_kelas);
					}
					
					// $save_class_score = $this->Scm->save_data(['class_group_id' => $s_id_kelas_kuliah], ['score_id' => $o_score_data->score_id]);
				}
				// else{
				// 	$del = $this->feederapi->post('DeletePesertaKelasKuliah', array(
				// 		'key' => $a_prep_krs
				// 	));

				// 	print_r($del);
				// 	print('<h1>Data</h1>');
				// 	print_r($o_score_data);
				// }
			}
		}else{
			$s_message = 'ID kelas kuliah / class_group_id '.$s_id_kelas_kuliah.' tidak ditemukan di portal!'.'<br>';
			if ($b_all_sync) {
				$this->s_message .= $s_message;
			}
			else {
				print($s_message);
			}
		}
	}

	public function list_univ_global()
	{
		$this->a_page_data['body'] = $this->load->view('misc/list_univ', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function get_prodi_univ($s_id_univ = false)
	{
		if ($this->input->is_ajax_request()) {
			$s_id_univ = $this->input->post('id_univ');
		}

		// $a_result = $this->feederapi->post('GetAllProdi');
		$a_result = $this->feederapi->post('GetAllProdi', array(
			'filter' => "id_perguruan_tinggi = '{$s_id_univ}'"
		));
		print('<pre>');var_dump($a_result);exit;
	}

	public function get_list_university()
	{
		$a_result = $this->feederapi->post('GetAllPT');
		
		if ($this->input->is_ajax_request()) {
			$a_list_result = false;
			if (($a_result->error_code == 0) AND (count($a_result->data) > 0)) {
				$a_list_result = $a_result->data;
			}

			print json_encode(['data' => $a_list_result]);
		}
		else {
			$a_list_result = $a_result->data;
			print('<table style="border:1">');
			print('<tr>');
			print('<td>id_perguruan_tinggi</td>');
			print('<td>kode_perguruan_tinggi</td>');
			print('<td>nama_perguruan_tinggi</td>');
			print('<td>nama_singkat</td>');
			print('</tr>');
			foreach ($a_list_result as $o_data) {
				print('<tr>');
				print('<td>'.$o_data->id_perguruan_tinggi.'</td>');
				print('<td>'.$o_data->kode_perguruan_tinggi.'</td>');
				print('<td>'.$o_data->nama_perguruan_tinggi.'</td>');
				print('<td>'.$o_data->nama_singkat.'</td>');
				print('</tr>');
			}
			print('</table>');
		}
	}

	public function start_sync_class_lecturer($s_id_kelas_kuliah, $b_all_sync = false)
	{
		// print('<pre>');
		$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', array(
			'filter' => "id_kelas_kuliah = '{$s_id_kelas_kuliah}'"
		));
		$a_employee_id_not_found_in_feeder = array();
		$a_employee_id_not_assigned_in_feeder = array();

		if (($a_get_detail_kelas_kuliah->error_code == 0) AND (count($a_get_detail_kelas_kuliah->data) > 0)) {
			$mbo_class_group_master = $this->Cgm->get_class_group_filtered(array('cmc.class_group_id' => $s_id_kelas_kuliah))[0];

			if ($mbo_class_group_master) {
				$mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $mbo_class_group_master->class_master_id));

				if ($mba_class_master_lecturer) {
					$a_employee_id_has_processed = array();

					foreach ($mba_class_master_lecturer as $o_class_lecturer) {
						$s_employee_id = (!is_null($o_class_lecturer->employee_id_reported)) ? $o_class_lecturer->employee_id_reported : $o_class_lecturer->employee_id;
						$o_employee_data = $this->EeM->get_employee_data(array('em.employee_id' => $s_employee_id))[0];

						if (!in_array($s_employee_id, $a_employee_id_has_processed)) {
							$a_get_penugasan_dosen = $this->feederapi->post('GetDetailPenugasanDosen', array(
								'filter' => "id_dosen = '{$s_employee_id}' AND id_tahun_ajaran = '{$mbo_class_group_master->running_year}'"
							));
							$s_id_registrasi_dosen = '';
							
							if ($a_get_penugasan_dosen->error_code != 0) {
								var_dump($a_get_penugasan_dosen);
								exit;
								
							}else if (count($a_get_penugasan_dosen->data) == 0) {
								$a_get_detail_dosen = $this->feederapi->post('GetListDosen', array(
									'filter' => "nidn = '{$o_employee_data->employee_lecturer_number}'"
								));

								if ($a_get_detail_dosen->error_code != 0) {
									var_dump($a_get_detail_dosen);
									exit;

								}else if (count($a_get_detail_dosen->data) > 0) {
									$a_get_detail_dosen_data = $a_get_detail_dosen->data[0];

									if ($s_employee_id != $a_get_detail_dosen_data->id_dosen) {
										$a_get_penugasan_dosen = $this->feederapi->post('GetDetailPenugasanDosen', array(
											'filter' => "id_dosen = '{$a_get_detail_dosen_data->id_dosen}' AND id_tahun_ajaran = '{$mbo_class_group_master->running_year}'"
										));

										if ($a_get_penugasan_dosen->error_code > 0) {
											var_dump($a_get_penugasan_dosen);
											exit;

										}else if (count($a_get_penugasan_dosen->data) == 0) {
											if (!in_array($s_employee_id, $a_employee_id_not_assigned_in_feeder)) {
												array_push($a_employee_id_not_assigned_in_feeder, $s_employee_id.'_'.$o_employee_data->personal_data_name);
											}
										}else{
											$a_data = $a_get_penugasan_dosen->data[0];
											$s_id_registrasi_dosen = $a_data->id_registrasi_dosen;
										}

										// $this->EeM->save_employee(array('employee_id' => $a_get_detail_dosen_data->id_dosen), $s_employee_id);
									}else{
										if (!in_array($s_employee_id, $a_employee_id_not_assigned_in_feeder)) {
											array_push($a_employee_id_not_assigned_in_feeder, $s_employee_id.'_'.$o_employee_data->personal_data_name);
										}
									}
								}else {
									if (!in_array($s_employee_id, $a_employee_id_not_found_in_feeder)) {
										array_push($a_employee_id_not_found_in_feeder, $s_employee_id.'_'.$o_employee_data->personal_data_name);
										// print('ini ada!<br>');var_dump($a_get_detail_dosen);exit;
									}
								}
							}else{
								$a_data = $a_get_penugasan_dosen->data[0];
								$s_id_registrasi_dosen = $a_data->id_registrasi_dosen;
							}

							if (!empty($s_id_registrasi_dosen)) {
								$mba_subject_delivered = $this->Cgm->get_class_subject_delivered(array('class_master_id' => $mbo_class_group_master->class_master_id));
								$i_count_subject_delivered = ($mba_subject_delivered) ? count($mba_subject_delivered) : 0;
								$i_count_realisasi = 0;
								// $i_rencana_pertemuan = (intval($o_class_lecturer->credit_allocation) * 14) / 7;
								if ($mba_subject_delivered) {
									$s_date_start = strtotime($mba_subject_delivered[0]->subject_delivered_time_start);
									$s_date_end = strtotime($mba_subject_delivered[count($mba_subject_delivered) - 1]->subject_delivered_time_end);
									$s_diff = $s_date_end - $s_date_start;
									$hari = $s_diff / 60 / 60 / 24;
									$i_count_realisasi = $hari / 7;
								}

								$a_dosen_pengajar_kelas = array(
									'id_registrasi_dosen' => $s_id_registrasi_dosen,
									'id_kelas_kuliah' => $s_id_kelas_kuliah,
									// 'id_substansi' => $mbo_class_group_master->id_substansi,
									'sks_substansi_total' => round($o_class_lecturer->credit_allocation, 2, PHP_ROUND_HALF_UP),
									'sks_tm_subst' => round($o_class_lecturer->credit_allocation, 2, PHP_ROUND_HALF_UP),
									'sks_prak_subst' => "0",
									'sks_prak_lap_subst' => "0",
									'sks_sim_subst' => "0",
									// 'rencana_minggu_pertemuan' => "".intval($o_class_lecturer->credit_allocation) * 14,
									'rencana_minggu_pertemuan' => "18",
									// 'realisasi_minggu_pertemuan' => "$i_count_subject_delivered",
									// 'realisasi_minggu_pertemuan' => "$i_count_realisasi",
									'id_jenis_evaluasi' => '1'
								);

								$a_get_dosen_pengajar_kelas = $this->feederapi->post('GetDosenPengajarKelasKuliah', array(
									'filter' => "id_registrasi_dosen = '{$s_id_registrasi_dosen}' AND id_kelas_kuliah = '{$s_id_kelas_kuliah}'"
								));

								if ($a_get_dosen_pengajar_kelas->error_code > 0) {
									var_dump($a_get_dosen_pengajar_kelas);
									exit;

								}else if (count($a_get_dosen_pengajar_kelas->data) !=  0) {
									$a_dosen_pengajar_kelas_data = $a_get_dosen_pengajar_kelas->data[0];
									$a_save_dosen_pengajar_kelas = $this->feederapi->post('UpdateDosenPengajarKelasKuliah', array(
										'key' => array(
											'id_aktivitas_mengajar' => $a_dosen_pengajar_kelas_data->id_aktivitas_mengajar
										),
										'record' => $a_dosen_pengajar_kelas
									));
								}else{
									$a_save_dosen_pengajar_kelas = $this->feederapi->post('InsertDosenPengajarKelasKuliah', array(
										'record' => $a_dosen_pengajar_kelas
									));
								}

								if ($a_save_dosen_pengajar_kelas->error_code != 0) {
									var_dump($a_save_dosen_pengajar_kelas);
									exit;
								}
								else {
									$s_message = 'dosen '.$o_employee_data->personal_data_name.' ditambahkan!'.'<br>';
									if ($b_all_sync) {
										$this->s_message .= $s_message;
									}
									else {
										print($s_message);
									}
									// print('<pre>');var_dump($a_save_dosen_pengajar_kelas);
								}
							}
							// var_dump($a_get_penugasan_dosen);
							array_push($a_employee_id_has_processed, $s_employee_id);
						}
					}
				}
			}
		}
		else{
			$s_message = 'class_id '.$s_id_kelas_kuliah.' is not synchronized to feeder!'.'<br>';
			if ($b_all_sync) {
				$this->s_message .= $s_message;
			}
			else {
				print($s_message);
			}
		}

		// $a_employee_id_not_found_in_feeder = array();
		// $a_employee_id_not_assigned_in_feeder = array();

		if (count($a_employee_id_not_found_in_feeder) > 0) {
			print('employee not found kelas '.$s_id_kelas_kuliah.'!:<br>');
			print_r($a_employee_id_not_found_in_feeder);exit;
		}

		if (count($a_employee_id_not_assigned_in_feeder) > 0) {
			print('employee not assigned in feeder kelas '.$s_id_kelas_kuliah.'!:<br>');
			print_r($a_employee_id_not_assigned_in_feeder);exit;
		}
	}

	public function check_test()
	{
		$mba_subject_delivered = $this->Cgm->get_class_subject_delivered(array('class_master_id' => 'c226cba8-de91-4b9b-bedc-5bd6bda963d8'));
		$s_date_start = strtotime($mba_subject_delivered[0]->subject_delivered_time_start);
		$s_date_end = strtotime($mba_subject_delivered[count($mba_subject_delivered) - 1]->subject_delivered_time_end);
		$s_diff = $s_date_end - $s_date_start;
		$hari = $s_diff / 60 / 60 / 24;
		$s_minggu = $hari / 7;
		print('<pre>');var_dump($s_minggu);exit;
	}

	public function start_sync_class_limited($s_academic_year_id, $s_semester_type_id, $page = 1)
	{
		$limit = 100;
		$l_start = $limit * ($page - 1);
		print "<pre>";
		$this->s_message = '';
		
		$mba_class_master_data = $this->Cgm->get_class_master_data_limit(false, [
			'academic_year_id' => $s_academic_year_id,
			'semester_type_id' => $s_semester_type_id
		], $l_start, $limit);
		
		// var_dump($mba_class_master_data);exit;
		if($mba_class_master_data){
			foreach($mba_class_master_data as $o_class_master){
				// $this->sync_class_master($o_class_master->class_master_id);
				$this->sync_master_class($o_class_master->class_master_id, true);
				$this->s_message .= '<p>============================================================================================</p>';
			}
			$this->s_message .= '.........................Finish.........................';
			print('\n Finish');


			$config = $this->config->item('mail_config');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from('employee@company.ac.id');
			$this->email->to('employee@company.ac.id');
			$this->email->subject('Sinkronisasi Feeder Log Report');
			$this->email->message($this->s_message);
			$this->email->send();
			exit;
		}
	}

	public function start_sync_class($s_academic_year_id, $s_semester_type_id)
	{
		print "<pre>";
		$this->s_message = '';
		// print('ada');
		$mba_class_master_data = $this->Cgm->get_class_master_data(false, [
			'academic_year_id' => $s_academic_year_id,
			'semester_type_id' => $s_semester_type_id
		]);
		
		if($mba_class_master_data){
			foreach($mba_class_master_data as $o_class_master){
				$mba_score_data = $this->General->get_where('dt_score', ['class_master_id' => $o_class_master->class_master_id]);
				if ($mba_score_data) {
					// $this->sync_class_master($o_class_master->class_master_id, true);
					$this->sync_master_class($o_class_master->class_master_id, true);
					$this->s_message .= '<p>============================================================================================</p>';
				}
			}
			$this->s_message .= '.........................Finish.........................';
			print('\n Finish');


			$config = $this->config->item('mail_config');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from('employee@company.ac.id');
			$this->email->to('employee@company.ac.id');
			$this->email->subject('Sinkronisasi Feeder Log Report');
			$this->email->message($this->s_message);
			$this->email->send();
			exit;
		}
	}

	public function check_semester_bentar($s_class_master_id)
	{
		// $a_batch_available = [2015, 2016, 2017, 2018]; // inih
		$a_batch_available = [2019]; // inih
		$mba_class_member = $this->General->get_where('dt_score', ['class_master_id' => $s_class_master_id]);
		if ($mba_class_member) {
			foreach ($mba_class_member as $o_score) {
				$mbo_student_data = $this->General->get_where('dt_student', ['student_id' => $o_score->student_id])[0];
				// var_dump($mbo_student_data);
				if (in_array($mbo_student_data->academic_year_id, $a_batch_available)) {
					return true;
				}
			}
			return false;
		}else{
			return false;
		}
	}

	public function test_class($s_class_master_id)
	{
		$o_class_master = $this->Cgm->get_class_master_data($s_class_master_id)[0];
		$s_academic_year_id = $o_class_master->academic_year_id;
		$s_semester_type_id = $o_class_master->semester_type_id;
		# pisahkan
		$mba_class_master_groups = $this->Cgm->get_class_master_group(array('class_master_id' => $s_class_master_id));
		if ($mba_class_master_groups) {
			foreach ($mba_class_master_groups as $o_class_master_groups) {
				$mba_class_subject_overload = $this->Cgm->get_class_by_offered_subject(array('cg.class_group_id' => $o_class_master_groups->class_group_id));
				if (($mba_class_subject_overload) AND (count($mba_class_subject_overload) > 1)) {
					$this->separates_class_group_feeder($o_class_master_groups->class_group_id);
				}
			}
		}
		# end
		$mba_class_groups = $this->Cgm->get_class_group_filtered([
			'cmc.class_master_id' => $o_class_master->class_master_id
		], true);

		print('<pre>');
		var_dump($mba_class_groups);
	}

	public function check_class_feeder()
	{
		$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
			'filter' => "id_prodi = '7da8cd1e-8f0e-41f4-89dd-361c29801087' AND id_semester = '20193' AND id_matkul = 'cb76a64e-65e3-44ff-87bb-ea960ea5243f'"
		]);
		print('<pre>');
		var_dump($a_get_detail_kelas_kuliah);
	}

	public function sync_class_master2($s_class_master_id)
	{
		$o_class_master = $this->Cgm->get_class_master_data($s_class_master_id)[0];

		if ($o_class_master) {
			$s_academic_year_id = $o_class_master->academic_year_id;
			$s_semester_type_id = $o_class_master->semester_type_id;

			$mba_class_groups = $this->Cgm->get_class_group_filtered([
				'cmc.class_master_id' => $o_class_master->class_master_id
			], true);

			$avail = true;
			if ($mba_class_groups AND $avail) {
				foreach ($mba_class_groups as $o_class_group) {
					if ($o_class_group->curriculum_subject_credit != 0) {
						if ($s_matkul_id = $this->_check_matkul_feeder($o_class_group)) {
							$s_study_program_id = (is_null($o_class_group->class_study_program_main_id)) ? $o_class_group->class_group_study_program_id : $o_class_group->class_study_program_main_id;
							$mbo_class_iuli_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_class_group->class_group_study_program_id])[0];
							$mbo_class_feeder_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id])[0];

							print($mbo_class_iuli_prodi->study_program_abbreviation);
							$s_semester_id = implode('', [$s_academic_year_id.$s_semester_type_id]);
							if (in_array($s_semester_type_id, ['7', '8'])) {
								$s_semester_id = $s_academic_year_id.'3';
							}

							$a_kelas_feeder_data = $this->feederapi->post('GetDetailKelasKuliah', [
								'filter' => "id_kelas_kuliah = '{$o_class_group->class_group_id}'"
							]);

							// $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
							// 	'filter' => "
							// 		id_prodi = '{$s_study_program_id}'
							// 		AND id_semester = '{$s_semester_id}'
							// 		AND id_matkul = '{$s_matkul_id}'"
							// ]);
	
							if ($a_kelas_feeder_data->error_code != 0) {
								print('<pre>');var_dump($a_kelas_feeder_data);exit;
							}
							else if (count($a_kelas_feeder_data->data) == 0) {
								print(' tidak ada'.'<br>');
							}
							else if (count($a_kelas_feeder_data->data) > 0) {
								print(' ada '.$mbo_class_feeder_prodi->study_program_abbreviation.'<br>');
							}
							
						}
					}
				}
			}
		}else{
			print('Class not found in database!');exit;
		}
	}

	// public function sync_custom_class()
	// {
	// 	$a_class_master_id = ['00b60548-463f-4a6f-a5a0-60d4bb430ade','081da4c8-6694-4b57-a780-d79ab202aafd','097dbf38-5ccd-4511-86ce-403c4779c7cd','1165e3fd-05ca-4a12-8cb0-d2dec053df75','123cb05c-506b-48e5-9914-3737088fe1c5','134d63fe-eeeb-4a71-8670-94f78b9a83f8','14a6ebc0-a112-45b8-83f7-fe2936bd0b25','1d747d07-1f97-4922-aee8-46c78af589bb','23375b33-b641-436c-ad9a-86243e8e24a5','26f6fbbf-f87d-42e9-99e3-1ffac707687f','2ac3cbd3-df2c-4fcd-b032-6abf06ad5388','2defd024-766d-452f-920f-0e07a776b189','3094b3dc-e20a-4d36-a578-dc330a23b352','338522ca-e1ff-423d-aefc-ce3384c3fd03','3823bd61-07fb-4997-8c88-22b77b9c875c','402a63c9-30ff-4c2c-9fab-fa2cc1b7ba51','434991aa-70d1-4ac2-9bf6-1649077ad6c1','497d6daa-9ec5-4441-a0b7-e3f5a5190942','4e2a030a-b361-4dd9-89e1-bbc433ee1ea8','510d0f90-8181-49fa-a392-f75305cddd14','55e1e068-2bc9-4d26-b60d-5332b43b029c','5706d649-eb10-46d3-add4-9619e369f624','588aa541-49aa-4185-bbce-7739358720d1','5de2f570-7803-45ed-95ad-b4257e136cd6','64d4bcc8-f9c2-4030-b13c-d9031b1d9978','69e62fcc-2a96-4c12-be92-1b63b8831df6','6d5cdecf-6bcd-4c75-bfe4-91fe221257b0','6d96d2ba-ba72-434c-87dc-976da29794b8','722c2225-f220-4efc-bdd3-8feb2043ed35','7280bc58-1291-4a7e-8ca9-1f9ec33b1178','79b2b22f-badd-4583-a0f2-2f63a189caa5','7d03e8ba-f88d-4319-a18d-c0832b596f43','84bce6f3-08e2-4751-bb58-0e773fae518b','88be8e2b-e6a3-459f-995a-b6807227fd87','8c41d95f-c782-4b19-9873-2490da2d8e3a','8ecdbf73-1961-4d5a-9271-a7b4005cfbe2','935ff486-7524-4675-8da5-c3049684c97d','93cf0b60-c772-437d-b1a9-cb6803a05ff0','9a1517ba-f19c-44db-9777-58c0a6c173f5','a04d4f78-e0bd-42a5-a5bb-25b8d18fd031','a4485690-762f-4264-a305-39e29dafe7bb','ac2304e6-4042-40c8-8704-e77fc4ae8b71','acdee5c5-4b74-481b-965e-6835c8993eed','b125725e-2cc1-4bd8-be52-6d204749b7a7','b4ee36c9-c302-4886-a248-d31f095fba8f','b7ef06b4-52f0-469c-a4a7-a287e7670ee7','babad672-5a52-484c-b821-b3438b597b91','bb74b329-e5fb-42df-b0da-ce60196742c1','bdd3c49e-e299-4f71-9d58-ed3362c59769','c2060716-bc58-4bb3-881b-666852a15ba8','c3de0e8e-4c71-4997-8462-9e6754a666b4','cea28f18-094e-4ccb-9a29-5bf9b297df74','d7b7a9cb-2819-4496-8880-6149a7a23c5c','d8c3731e-92db-42e7-9ea7-ab0b267e569a','dd3a4b93-273c-402a-b0db-23fabd96b451','dd4654f6-8ab5-4986-9cf9-847ae0ec0fd3','de3ffbbf-2f11-41b9-8b03-27015212a78d','e2e31aac-199e-4509-a1a7-2e7e249ca9f9','f30e254a-8338-4de9-80cc-af0365d07b35','f32c7bb8-a116-47ce-9da8-06a5b51ef3c6','f4cd3127-b2b1-4dfd-bd98-e2c35ffebf1b','f82ea2f1-6755-4d9b-bc38-441f1cca960b','fe4e211d-a199-47de-9363-9a096ae739e1'];
	// 	foreach ($a_class_master_id as $s_class_master_id) {
	// 		$this->sync_class_master($s_class_master_id);
	// 	}
	// }

	public function test_get_detail_mahasiswa()
	{
		// FERA
		$get_data = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', [
			'filter' => "nama_ibu_kandung = 'FERA'"
		]);
		print('<pre>');var_dump($get_data);exit;
	}

	public function update_riwayat_pendidikan($s_student_id)
	{
		$a_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
		$a_student_data = $a_student_data[0];
		$a_prep_data = [
			'id_registrasi_mahasiswa' => $s_student_id,
			'id_mahasiswa' => $a_student_data->personal_data_id,
			'nim' => $a_student_data->student_number,
			'id_jenis_daftar' => '2',
			'id_jalur_daftar' => '6',
			'id_periode_masuk' => $a_student_data->academic_year_id.'1',
			'tanggal_daftar' => '2019-08-30',
			'id_perguruan_tinggi' => '7ed83401-2862-4e12-aa60-79defe7b90a8',
			'id_prodi' => '903eb8ee-159e-406b-8f7e-38d63a961ea4',
			"id_bidang_minat"=> null,
			"sks_diakui" => "90",
			"id_perguruan_tinggi_asal"=> "edb5f561-e524-417f-b1ac-ca2392c059bd",
			"id_prodi_asal"=> "0cbfe67d-6139-4129-b1cf-c9db06734d24",
			// "nama_prodi_asal": "",
			"id_pembiayaan"=> "1",
			"biaya_masuk"=> 30000000
		];
		$force_update = $this->feederapi->post('UpdateRiwayatPendidikanMahasiswa', [
			'key' => array(
				"id_registrasi_mahasiswa" => $s_student_id
			),
			'record' => $a_prep_data,
		]);
		print('<pre>');var_dump($force_update);exit;
	}

	public function delete_all_kelas()
	{
		$a_get_detail_kelas_kuliah = $this->feederapi->post('GetListKelasKuliah', [
			'filter' => "id_semester = '20213'"
		]);

		// print('<pre>');
		// if (($a_get_detail_kelas_kuliah->error_code == 0) && (count($a_get_detail_kelas_kuliah->data) > 0)) {
		// 	foreach ($a_get_detail_kelas_kuliah->data as $o_kelas) {
		// 		// var_dump($o_kelas);
		// 		$a_delete_matkul_kurikulum = $this->feederapi->post('DeleteKelasKuliah', [
		// 			'key' => [
		// 				'id_kelas_kuliah' => $o_kelas->id_kelas_kuliah
		// 			]
		// 		]);

		// 		var_dump($a_delete_matkul_kurikulum);
		// 		// if ($a_get_detail_kelas_kuliah->error_code == 0) {
		// 		// 	print('kelas '.$o_kelas->nama_kelas_kuliah.' '.$o_kelas->nama_mata_kuliah.' dihapus');
		// 		// }
		// 		// else {
		// 		// 	print('gagal hapus kelas '.$o_kelas->nama_kelas_kuliah.' '.$o_kelas->nama_mata_kuliah);
		// 		// }
		// 		print('<br>');
		// 	}
		// }
		// exit;
		print('<pre>');var_dump($a_get_detail_kelas_kuliah);exit;
	}

	public function delete_semua_kelas_kuliah($s_tahun_ajaran)
	{
		// print('closed!');exit;
		$mba_list_kelas_kuliah = $this->feederapi->post('GetListKelasKuliah', array(
			'filter' => "id_semester = '$s_tahun_ajaran'"
		));

		if (($mba_list_kelas_kuliah->error_code == 0) AND (count($mba_list_kelas_kuliah->data) > 0)) {
			$a_data_kelas = $mba_list_kelas_kuliah->data;
			foreach ($a_data_kelas as $o_kelas) {
				$this->delete_kelas_kuliah($o_kelas->id_kelas_kuliah);
				print('<br>');
			}
			// print('<pre>');var_dump($mba_list_kelas_kuliah);exit;
		}
	}

	public function get_test($s_id_kelas_kuliah)
	{
		// $result_data = $this->feederapi->post('GetPesertaKelasKuliah', array(
		// 	'filter' => "id_kelas_kuliah = '$s_id_kelas_kuliah'"
		// ));

		$result_data = $this->feederapi->post('GetDosenPengajarKelasKuliah', array(
			'filter' => "id_kelas_kuliah = '$s_id_kelas_kuliah'"
		));

		// $result_data = $this->feederapi->post('DeleteKelasKuliah', [
		// 	'key' => [
		// 		'id_kelas_kuliah' => $s_id_kelas_kuliah
		// 	]
		// ]);

		print('<pre>');var_dump($result_data);exit;
	}

	public function delete_kelas_kuliah($s_id_kelas_kuliah, $b_all_sync = false)
	{
		// $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', array(
		// 	'filter' => "id_kelas_kuliah = '$s_id_kelas_kuliah'"
		// ));

		$a_riwayat_nilai_mahasiswa = $this->feederapi->post('GetPesertaKelasKuliah', array(
			'filter' => "id_kelas_kuliah = '$s_id_kelas_kuliah'"
		));

		if (($a_riwayat_nilai_mahasiswa->error_code == 0) AND (count($a_riwayat_nilai_mahasiswa->data) > 0)) {
			$a_feeder_data = $a_riwayat_nilai_mahasiswa->data;
			foreach ($a_feeder_data as $o_riwayat_nilai) {
				$remove_student_kelas = $this->feederapi->post('DeletePesertaKelasKuliah', array(
					'key' => array(
						'id_kelas_kuliah' => $s_id_kelas_kuliah,
						'id_registrasi_mahasiswa' => $o_riwayat_nilai->id_registrasi_mahasiswa
					)
				));
			}
		}

		$a_aktivitas_mengajar_dosen = $this->feederapi->post('GetDosenPengajarKelasKuliah', array(
			'filter' => "id_kelas_kuliah = '$s_id_kelas_kuliah'"
		));

		if (($a_aktivitas_mengajar_dosen->error_code == 0) AND (count($a_aktivitas_mengajar_dosen->data) > 0)) {
			$a_feeder_data = $a_aktivitas_mengajar_dosen->data;
			foreach ($a_feeder_data as $o_aktivitas_mengajar) {
				$remove_dosen_kelas = $this->feederapi->post('DeleteDosenPengajarKelasKuliah', array(
					// 'id_aktivitas_mengajar' => $o_aktivitas_mengajar->id_aktivitas_mengajar
					'key' => array(
						'id_kelas_kuliah' => $s_id_kelas_kuliah,
						'id_aktivitas_mengajar' => $o_aktivitas_mengajar->id_aktivitas_mengajar
					)
				));
				// print('<pre>');var_dump($remove_dosen_kelas);
				// print('<br>');
			}
		}

		$delete_kelas_kuliah = $this->feederapi->post('DeleteKelasKuliah', [
			'key' => [
				'id_kelas_kuliah' => $s_id_kelas_kuliah
			]
		]);

		if ($delete_kelas_kuliah->error_code == 0) {
			$s_message = 'delete kelas '.$s_id_kelas_kuliah.'<br>';
			if ($b_all_sync) {
				$this->s_message .= $s_message;
			}
			else {
				print($s_message);
			}
			return true;
		}
		else {
			print('gagal delete kelas '.$s_id_kelas_kuliah.'<br><pre>');
			var_dump($delete_kelas_kuliah);
			// exit;
			return false;
		}
	}

	public function getDictionary($s_fungsi)
	{
		$a_kamus = $this->feederapi->post('GetDictionary', array(
			'fungsi' => $s_fungsi
		));

		if ($a_kamus->error_code == 0) {
			$data = $a_kamus->data;
			$response_data = $data->response;
			print('<h4>'.$s_fungsi.'</h4>');
			print('<table border="1">');
			if (isset($data->request)) {
				$request_data = $data->request;
				print('<tr><th colspan="5">Request Data</th></tr>');
				print('<tr><th>Key</th><th>Type</th><th>Primary</th><th>Nullable</th><th>Description</th></tr>');
				foreach ($request_data as $key => $value) {
					if (is_string($value)) {
						print('<tr><td colspan="5">'.$key.'</td></tr>');
					}
					else {
						print('<tr>');
						print('<td>'.$key.'</td>');
						print('<td>'.$value->type.'</td>');
						print('<td>'.$value->primary.'</td>');
						print('<td>'.$value->nullable.'</td>');
						print('<td>'.$value->keterangan.'</td>');
						print('</tr>');
					}
				}
			}
			if (isset($data->response)) {
				$response_data = $data->response;
				print('<tr><th colspan="5">Response Data</th></tr>');
				print('<tr><th>Key</th><th>Type</th><th>Primary</th><th>Nullable</th><th>Description</th></tr>');
				foreach ($response_data as $key => $value) {
					if (is_string($value)) {
						print('<tr><td colspan="5">'.$key.'</td></tr>');
					}
					else {
						print('<tr>');
						print('<td>'.$key.'</td>');
						print('<td>'.$value->type.'</td>');
						print('<td>'.$value->primary.'</td>');
						print('<td>'.$value->nullable.'</td>');
						print('<td>'.$value->keterangan.'</td>');
						print('</tr>');
					}
				}
			}
			print('</table>');
		}
		else {
			print('<pre>');var_dump($a_kamus);exit;
		}
	}

	public function get_class_study_program_lecturer($s_class_master_id)
	{
		$mba_class_master_class = $this->General->get_where('dt_class_master_class', ['class_master_id' => $s_class_master_id]);
		// $mba_class_master_class = $this->Cgm->get_class_group_master_lists(['class_master_id' => $s_class_master_id]);
		// print('<pre>');var_dump($mba_class_master_class);exit;
		if ($mba_class_master_class) {
			$mba_class_master_detail = $this->Cgm->check_get_class_master_filtered([
				'cm.class_master_id' => $s_class_master_id
			]);
			foreach ($mba_class_master_class as $o_class_master) {
				$mba_class_group_detail = $this->Cgm->get_class_group_filtered([
					'cmc.class_group_id' => $o_class_master->class_group_id
				], true);
				print('---------------------'.$mba_class_master_detail[0]->class_master_name.'--------------------<br>');
				if ($mba_class_group_detail) {
					foreach ($mba_class_group_detail as $o_class_group) {
						print('-'.$o_class_group->class_group_name.'<br>');
					}
				}
			}
		}
	}

	public function get_details_nilai_perkuliahan()
	{
		$a_data_nilai_perkuliahan_kelas = $this->feederapi->post('GetDetailNilaiPerkuliahanKelas', array(
			'filter' => "id_kelas_kuliah = 'e447db85-96db-495c-aaf5-97f7d5f5f1d6' AND id_registrasi_mahasiswa = 'ff13416c-0ec5-4115-8a7d-acaf6e99b183'"
		));

		print('<pre>');
		var_dump($a_data_nilai_perkuliahan_kelas);
	}

	public function update_nilai_perkuliahan()
	{
		$a_prep_nilai_perkuliahan = [
			'nilai_angka' => '89.0',
			'nilai_indeks' => '4.00',
			'nilai_huruf' => 'A'
		];

		$a_prep_krs = array(
			'id_registrasi_mahasiswa' => 'ff13416c-0ec5-4115-8a7d-acaf6e99b183',
			'id_kelas_kuliah' => 'e447db85-96db-495c-aaf5-97f7d5f5f1d6'
		);
		
		$o_update_nilai_perkuliahan_kelas = $this->feederapi->post('UpdateNilaiPerkuliahanKelas', array(
			'record' => $a_prep_nilai_perkuliahan,
			'key' => $a_prep_krs
		));
		print('<pre>');
		var_dump($o_update_nilai_perkuliahan_kelas);
	}

	public function sync_semester_pendek($s_academic_year_id)
	{
		$mba_odd_class = $this->Cgm->get_class_master_data(false, [
			'academic_year_id' => $s_academic_year_id,
			'semester_type_id' => 7
		]);

		// $mba_even_class = $this->Cgm->get_class_master_data(false, [
		// 	'academic_year_id' => $s_academic_year_id,
		// 	'semester_type_id' => 8
		// ]);

		if ($mba_odd_class) {
			foreach ($mba_odd_class as $o_class) {
				$class_member = $this->Cgm->get_class_master_student($o_class->class_master_id, [
					'score_approval' => 'approved'
				]);
				if ($class_member) {
					$this->sync_semester_pendek_firts($o_class);
					print($o_class->class_master_name.'<br>');
					// exit;
				}
			}
		}

		// if ($mba_even_class) {
		// 	foreach ($mba_even_class as $o_class) {
		// 		$class_member = $this->Cgm->get_class_master_student($o_class->class_master_id, [
		// 			'score_approval' => 'approved'
		// 		]);
		// 		if ($class_member) {
		// 			$this->sync_semester_pendek_firts($o_class);
		// 			print($o_class->class_master_name.'<br>');
		// 		}
		// 	}
		// }

		// print('<pre>');var_dump($a_get_detail_kelas_kuliah);exit;
	}

	function sync_semester_pendek_individual($s_class_master_id) {
		$mba_class = $this->Cgm->get_class_master_data(false, [
			'class_master_id' => $s_class_master_id
		]);
		if ($mba_class) {
			$class_member = $this->Cgm->get_class_master_student($s_class_master_id, [
				'score_approval' => 'approved'
			]);
			if ($class_member) {
				$this->sync_semester_pendek_firts($mba_class[0]);
				print($mba_class[0]->class_master_name.'<br>');
			}
		}
		else {
			print('<pre>');var_dump('kelas tidak ditemukan');
		}
	}

	public function get_mahasiswa()
	{
		$a_get_list_mahasiswa = $this->feederapi->post('GetListMahasiswa', [
			'filter' => "id_status_mahasiswa IS NULL AND id_prodi = '208c8d88-2560-4640-a1b2-bfd42b0e7c16'",
			'order' => "id_periode DESC"
		]);

		$a_data_mahasiswa = $a_get_list_mahasiswa->data;
		if (count($a_data_mahasiswa) > 0) {
			foreach ($a_data_mahasiswa as $o_mahasiswa) {
				$a_get_perkuliahan_mahasiswa = $this->feederapi->post('GetListPerkuliahanMahasiswa', [
					'filter' => "id_registrasi_mahasiswa = '".$o_mahasiswa->id_registrasi_mahasiswa."'"
				]);
				print('<pre>');var_dump($a_get_perkuliahan_mahasiswa);exit;
			}
		}
		// print('<pre>');var_dump($a_get_list_mahasiswa);exit;
	}

	function get_student() {
		$s_personal_data_id = '3898a159-e5b1-4aa7-9f56-fe755db53fc6';
		$a_data = $this->feederapi->post('GetListRiwayatPendidikanMahasiswa', [
			'filter' => "id_mahasiswa = '".$s_personal_data_id."'"
		]);
		print('<pre>');var_dump($a_data);
	}
	
	public function sync_semester_pendek_firts($o_class)
	{
		$s_academic_year_id = $o_class->academic_year_id;
		$s_semester_type_id = $o_class->semester_type_id;

		$mba_class_groups = $this->Cgm->get_class_group_filtered([
			'cmc.class_master_id' => $o_class->class_master_id
		], true);
		// print('<pre>');var_dump($mba_class_groups);exit;

		if($mba_class_groups) {
			foreach($mba_class_groups as $o_class_group) {
				if ($o_class_group->curriculum_subject_credit != 0) {
					if ($o_class_group->curriculum_subject_type != 'extracurricular') {
						if($s_matkul_id = $this->_check_matkul_feeder($o_class_group)) {
							$s_study_program_id = (is_null($o_class_group->class_study_program_main_id)) ? $o_class_group->class_group_study_program_id : $o_class_group->class_study_program_main_id;
							$mbo_class_iuli_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_class_group->class_group_study_program_id])[0];
							$s_semester_id = $s_academic_year_id.'3';

							$s_nama_kelas = $mbo_class_iuli_prodi->study_program_abbreviation.$o_class_group->semester_id.$o_class_group->semester_type_id;

							$a_data_kelas_kuliah = [
								'id_prodi' => $s_study_program_id,
								'id_semester' => $s_semester_id,
								'id_matkul' => $s_matkul_id,
								'nama_kelas_kuliah' => $s_nama_kelas
							];

							$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
								'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_semester_id}' AND id_matkul = '{$s_matkul_id}' AND nama_kelas_kuliah = '$s_nama_kelas'"
							]);
							// print('<pre>');var_dump($a_get_detail_kelas_kuliah);exit;

							$b_ok = false;
							if(count($a_get_detail_kelas_kuliah->data) > 0) {
								$o_kelas_feeder_data = $a_get_detail_kelas_kuliah->data[0];
								if ($this->delete_kelas_kuliah($o_kelas_feeder_data->id_kelas_kuliah)) {
									$b_ok = true;
								}
							}
							else if(count($a_get_detail_kelas_kuliah->data) == 0) {
								$b_ok = true;
							}

							if ($b_ok) {
								$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
									'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_semester_id}' AND id_matkul = '{$s_matkul_id}' AND nama_kelas_kuliah = '$s_nama_kelas'"
								]);
	
								if(($a_get_detail_kelas_kuliah->error_code == 0) && (count($a_get_detail_kelas_kuliah->data) == 0)){
									$a_insert_kelas_kuliah = $this->feederapi->post('InsertKelasKuliah', [
										'record' => $a_data_kelas_kuliah
									]);
									
									if($a_insert_kelas_kuliah->error_code == 0){
										$this->Cgm->save_data([
											'class_group_id' => $a_insert_kelas_kuliah->data->id_kelas_kuliah
										], $o_class_group->class_group_id);
										$s_id_kelas_kuliah = $a_insert_kelas_kuliah->data->id_kelas_kuliah;
		
										$s_message = $o_class_group->class_group_id.' -> '.$a_insert_kelas_kuliah->data->id_kelas_kuliah.'<br>';
										// if ($b_all_sync) {
										// 	$this->s_message .= $s_message;
										// }
										// else {
											print($s_message);
										// }
									}
									else{
										print('err: 100-'.$o_class->class_master_id);
										print('<pre>');var_dump($a_insert_kelas_kuliah);
										// exit;
									}
								}else if(count($a_get_detail_kelas_kuliah->data) > 0){
									print('kelas masih ada<br>');
									print('<pre>');var_dump($a_get_detail_kelas_kuliah);
									// exit;
								}
	
								if(!is_null($s_id_kelas_kuliah)){
									$this->start_sync_score($s_academic_year_id, $s_semester_type_id, $s_matkul_id, $s_id_kelas_kuliah);
									$this->start_sync_class_lecturer($s_id_kelas_kuliah);
									// print($o_class_group->class_group_id.' jadi kelas '.$s_id_kelas_kuliah.'; matkul: '.$s_matkul_id.'; prodi: '.$s_study_program_id);
									// print('<br>');
								}
								// else{
								// 	print('id_kelas_kuliah is null:<br>');
								// 	var_dump($o_class_group);
								// 	exit;
								// }
							}
						}
						else {
							if ($o_class_group->curriculum_subject_type != 'extracurricular') {
								print('<pre>');
								print('001e<br>');
								var_dump($o_class_group);exit;
							}
							else {
								print('ett');exit;
							}
						}
					}
				}
				else{
					print('credit kosong!');exit;
				}
			}
		}
		// else{
		// 	print('kelas group kosong!');exit;
		// }
	}

	public function test_data()
	{
		$a_data_forlap = $this->feederapi->post('GetMahasiswaBimbinganDosen', [
			'filter' => "nama_dosen='NORMALISA' AND nidn='0405028303'"
		]);

		print('<pre>');var_dump($a_data_forlap);exit;
	}

	public function test_dosen()
	{
		$a_data_forlap = $this->feederapi->post('GetListDosen', [
			'filter' => "id_perguruan_tinggi='2e629b15-90b0-4001-95d0-026a30c8d814'"
		]);

		print('<pre>');var_dump($a_data_forlap);exit;
	}

	// public function sync_period_study($s_year = '2023', $s_semester_type = '1') {
	// 	$mba_prodi_list = $this->General->get_where('ref_study_program', ['study_program_main_id' => NULL]);
	// 	if ($mba_prodi_list) {
	// 		$s_semester_dikti = $s_year.$s_semester_type;
	// 		foreach ($mba_prodi_list as $o_prodi) {
	// 			print($o_prodi->study_program_name);
	// 			$has_sync_forlap = $this->feederapi->post('GetListPeriodePerkuliahan', [
	// 				'filter' => "id_prodi='$o_prodi->study_program_id' AND id_semester='$s_semester_dikti'"
	// 			]);
	// 			$a_filter_student = [
	// 				'study_program_id' => $o_prodi->study_program_id,
	// 				'academic_year_id' => $s_year,
	// 				'program_id' => '1'
	// 			];
	// 			$a_filter_active = $a_filter_student;
	// 			$a_filter_active['student_status'] = 'active';
	// 			$mba_student_register = $this->General->get_where('dt_student', $a_filter_student);
	// 			$mba_student_active = $this->General->get_where('dt_student', $a_filter_active);
	// 			if ($has_sync_forlap->error_code == 0) {
	// 				if (count($has_sync_forlap->data) > 0) {
	// 					print(' _ada');
	// 				}
	// 				else {
	// 					$a_periode_data = [
	// 						'id_prodi' => $o_prodi->study_program_id,
	// 						'id_semester' => $s_semester_dikti,
	// 						'jumlah_target_mahasiswa_baru' => '50',
	// 						'jumlah_pendaftar_ikut_seleksi' => ($mba_student_register) ? count($mba_student_register) : 0,
	// 						'jumlah_pendaftar_lulus_seleksi' => '',
	// 						'jumlah_daftar_ulang' => ($mba_student_active) ? count($mba_student_active) : 0,
	// 						'jumlah_mengundurkan_diri' => '',
	// 						'tanggal_awal_perkuliahan' => '04-09-2023',
	// 						'tanggal_akhir_perkuliahan' => '16-12-2023',
	// 					];

	// 					print(' _ga ada');
	// 				}
	// 			}
	// 			else {
	// 				print('<pre>ERROR<br>');var_dump($has_sync_forlap);exit;
	// 			}
	// 			print('<br>');
	// 		}
	// 	}
	// }

	public function sync_class_group_single($s_class_group_id)
	{
		$mba_class_groups = $this->Cgm->get_class_group_filtered([
			'cmc.class_group_id' => $s_class_group_id
		], true);
		print('<pre>');
		if ($mba_class_groups) {
			$this->sync_class_group($mba_class_groups[0], false, 2022, 1, false);
		}
		else {
			print('kelas ga ada!');
		}
	}

	public function test_class_master($s_academic_year = 2022, $s_semester_type_id = 2, $d_limit_start = 1)
	{
		$this->db->from('dt_class_master cm');
		$this->db->join('dt_score sc', 'sc.class_master_id = cm.class_master_id');
		$this->db->where(['cm.academic_year_id' => $s_academic_year, 'cm.semester_type_id' => $s_semester_type_id, 'sc.score_approval' => 'approved']);
		$this->db->order_by('cm.class_master_id');
		$this->db->group_by('cm.class_master_id');
		$this->db->limit(1000, ($d_limit_start - 1));
		$query = $this->db->get();
		// print($this->db->last_query());exit;
		if ($query->num_rows() > 0) {
			$class_master_data = $query->result();
			$i_numb = $d_limit_start;
			foreach ($class_master_data as $o_class_master) {
				// $mba_score_data = $this->General->get_where('dt_score', ['class_master_id' => $o_class_master->class_master_id, 'score_approval' => 'approved']);
				// if ($mba_score_data) {
					print($i_numb++.'. '.$o_class_master->class_master_name.'...............................');
					print('<br>');
					$this->sync_master_class($o_class_master->class_master_id);
					print('<br><br>');
				// }
			}
			// print('<pre>');var_dump($class_master_data);
		}
	}

	public function insertskalanilaicse() {
		// $a_get_skala = $this->feederapi->post('GetListSkalaNilaiProdi', [
		// 	'filter' => "id_prodi = '12c9ec75-af4a-46a1-ae12-b1ba4bf75c89'"
		// ]);
		// if (($a_get_skala->error_code == 0) AND (count($a_get_skala->data) > 0)) {
		// 	$mb_data = $a_get_skala->data;
		// 	foreach ($mb_data as $skala) {
		// 		$a_data = [
		// 			'id_prodi' => '2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8',
		// 			'nilai_huruf' => $skala->nilai_huruf,
		// 			'nilai_indeks' => $skala->nilai_indeks,
		// 			'bobot_minimum' => $skala->bobot_minimum,
		// 			'bobot_maksimum' => $skala->bobot_maksimum,
		// 			'tanggal_mulai_efektif' => $skala->tanggal_mulai_efektif,
		// 			'tanggal_akhir_efektif' => $skala->tanggal_akhir_efektif,
		// 		];

		// 		$a_insertskalanilai = $this->feederapi->post('InsertSkalaNilaiProdi', [
		// 			'record' => $a_data
		// 		]);
		// 		if ($a_insertskalanilai->error_code == 0) {
		// 			print($skala->nilai_huruf.': '.$skala->bobot_minimum.'-'.$skala->bobot_maksimum);
		// 		}
		// 		else {
		// 			print('<pre>');var_dump($a_insertskalanilai);
		// 		}
		// 		print('<br>');
		// 	}
		// }
		// else {
		// 	print('^_^!');
		// }
		// print('<pre>');var_dump($a_get_skala);exit;
	}

	function custom() {
		$a_class_master_id = ['0259f4f6-34fa-4200-89f3-451f2c6fac5b','04f04753-de94-4096-b971-c14611e45512','1f1ec3d6-5fdb-4389-9cff-6bead44efb20','230cd9c9-ecf9-43d9-8247-7f952e6aa029','2b2347bf-be14-45d1-ad96-1b7131930d45','33918871-924e-4715-b4ce-1734590786ef','3b42e942-1f0a-4adc-9168-2e85fac27210','4ddf616d-2d56-4ef9-97a4-afc1dd23b00f','60fef45c-cbed-4a21-92d7-a64798313668','78ca63e8-52f9-4223-9fb3-aa71c9b72730','80da6ecf-2a85-4cd8-aeb6-6b1e609785a8','8a795263-4951-4af7-b96e-d3698f59b7bf','8b7fab9b-042e-47c1-996b-c335bdd36d69','91612250-9671-459c-be6a-f4954913ccc9','97298d4c-e64f-4727-babd-4f92e37d0788','b5afa700-d2bb-411c-a64f-89b649df81e0','c053277e-2a45-4d7c-90a3-7af8a90f17dc','c4f81040-7666-47e4-a5ce-8664d38e57ec','cb041de7-0dbd-4181-b36a-e9d9fcda59e5','e0a5972a-fc91-41a5-b3f6-3b5e6d5da5b3','e12636df-cffe-467f-b0a9-8c72f180b7fb','e66727f7-0a4b-40b7-a32b-d4f6b44cada3','f0f0c302-8cd2-40aa-b769-76febb0d31a8','f44df080-50f1-4b7a-b260-5b312015cef4'];
		foreach ($a_class_master_id as $s_class_master_id) {
			$this->sync_master_class($s_class_master_id);
		}
	}

	public function sync_master_class($s_class_master_id, $b_all_sync = false)
	{
		// inih yang dipake
		// $a_prodi_request = ['6ce5bc8b-10f5-456d-855d-aef18dc641f4'];
		$a_prodi_request = false;
		$o_class_master = $this->Cgm->get_class_master_data($s_class_master_id)[0];
		$s_academic_year_id = $o_class_master->academic_year_id;
		$s_semester_type_id = $o_class_master->semester_type_id;

		$mba_class_groups = $this->Cgm->get_class_group_filtered([
			'cmc.class_master_id' => $o_class_master->class_master_id
		], true);
		print('<pre>');
		// var_dump($mba_class_groups);exit;

		// $b_semester_unrequest = $this->check_semester_bentar($s_class_master_id); 
		$b_semester_unrequest = true;

		$i_number_loop = 1;
		if($mba_class_groups AND $b_semester_unrequest) {
			foreach($mba_class_groups as $o_class_group){
				// $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
				// 	'filter' => "id_kelas_kuliah = '{$o_class_group->class_group_id}'"
				// ]);
				// if ($a_get_detail_kelas_kuliah->error_code == 0) {
				// 	print($i_number_loop++.'---------------------------'.$o_class_group->class_group_name.'-'.$o_class_group->study_program_abbreviation.'/'.$o_class_group->study_program_id.'---------------------------------<br>');
				// 	if (count($a_get_detail_kelas_kuliah->data) == 0) {
						$this->sync_class_group($o_class_group, $b_all_sync, $s_academic_year_id, $s_semester_type_id, $a_prodi_request);
				// 	}
				// 	else {
				// 		$this->sync_update_class_group($o_class_group->class_group_id, $s_class_master_id);
				// 	}
				// }
				// else {
				// 	print($i_number_loop++.'---------------------------'.$o_class_group->class_group_name.'-'.$o_class_group->study_program_abbreviation.'/'.$o_class_group->study_program_id.'---------------------------------<br>');
				// 	print('<pre>');var_dump($a_get_detail_kelas_kuliah);
				// 	print('<br>');
				// }

				// print('<pre>');var_dump($o_class_group);exit;
				// $mba_study_program_childdata = $this->Cgm->get_class_group_filtered([
				// 	'rspc.study_program_main_id' => $o_class_group->study_program_id,
				// 	'dcg.academic_year_id' => $s_academic_year_id,
				// 	'dcg.semester_type_id' => $s_semester_type_id,
				// 	'sn.subject_name' => $o_class_group->subject_name
				// ], true);
				// if ($mba_study_program_childdata) {
				// 	foreach ($mba_study_program_childdata as $o_classprodi) {
				// 		print('s---------------------------'.$o_classprodi->study_program_name.'---------------------------------s<br>');
				// 		// $this->sync_class_group($o_classprodi, $b_all_sync, $s_academic_year_id, $s_semester_type_id, $a_prodi_request);
				// 	}
				// }
				// else {
				// 	print($i_number_loop++.'---------------------------'.$o_class_group->class_group_name.'-'.$o_class_group->study_program_abbreviation.'/'.$o_class_group->study_program_id.'---------------------------------<br>');
				// 	$this->sync_class_group($o_class_group, $b_all_sync, $s_academic_year_id, $s_semester_type_id, $a_prodi_request);
				// }
			}
		}
		// else {
		// 	print('Class selected not found!');exit;
		// }
	}

	public function sync_update_class_group($s_class_group_id, $s_class_master_id)
	{
		$a_score_param = [
			'class_master_id' => $s_class_master_id,
			'class_group_id' => $s_class_group_id,
			'score_approval' => 'approved'
		];
		$mba_score_data = $this->Scm->get_score_data($a_score_param);
		if ($mba_score_data) {
			foreach ($mba_score_data as $o_score) {
				$a_get_detail_nilai = $this->feederapi->post('GetPesertaKelasKuliah', [
					'filter' => "id_kelas_kuliah = '{$o_score->class_group_id}' AND id_registrasi_mahasiswa = '$o_score->student_id'"
				]);
				// print('<pre>');var_dump($a_get_detail_nilai);exit;
				if (($a_get_detail_nilai->error_code == 0) AND (count($a_get_detail_nilai->data) > 0)) {
					$score_sum = round($o_score->score_sum, 1, PHP_ROUND_HALF_UP);
					$grade_point = round($this->grades->get_grade_point($score_sum), 2, PHP_ROUND_HALF_UP);
					$grade = $this->grades->get_grade($score_sum);
					
					$o_update_mata_kuliah = $this->feederapi->post('UpdateNilaiPerkuliahanKelas', [
						'record' => [
							'nilai_angka' => $score_sum,
							'nilai_indeks' => $grade_point,
							'nilai_huruf' => $grade,
						],
						'key' => [
							'id_registrasi_mahasiswa' => $o_score->student_id,
							'id_kelas_kuliah' => $o_score->class_group_id
						]
					]);

					if($o_update_mata_kuliah->error_code != 0){
						print('gagal update nilai<pre>');var_dump($o_update_mata_kuliah);exit;
					}
					else {
						print('Berhasil update nilai '.$o_score->student_email.'<br>');
					}
				}
				else if ($a_get_detail_nilai->error_code > 0) {
					print('<pre>');var_dump($a_get_detail_nilai);exit;
				}
				else {
					print('Data mahasiswa '.$o_score->student_email.' tidak ditemukan di feeder!!<br>');
				}
			}
		}
		// print('<pre>');var_dump($mba_score_data);exit;
	}

	function get_student_krs() {
		$s_id_reg_mahasiswa = "cf35718b-8cc2-4d84-a6cc-29b866c8115c";
		$a_getdata = $this->feederapi->post('GetKRSMahasiswa', [
			'filter' => "id_registrasi_mahasiswa = '{$s_id_reg_mahasiswa}' AND id_periode = '20222'"
		]);

		print('<pre>');var_dump($a_getdata);exit;
	}

	private function sync_class_group($o_class_group, $b_all_sync = false, $s_academic_year_id, $s_semester_type_id, $a_prodi_request = false)
	{
		if ($o_class_group->curriculum_subject_credit != 0) {
			if ($o_class_group->curriculum_subject_type != 'extracurricular') {
				if($s_matkul_id = $this->_check_matkul_feeder($o_class_group)){
					$s_study_program_id = (is_null($o_class_group->class_study_program_main_id)) ? $o_class_group->class_group_study_program_id : $o_class_group->class_study_program_main_id;
					$mbo_class_iuli_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_class_group->class_group_study_program_id])[0];
					$s_semester_id = implode('', [$s_academic_year_id.$s_semester_type_id]);
					if (in_array($s_semester_type_id, ['7', '8'])) {
						$s_semester_id = $s_academic_year_id.'3';
					}

					$b_continue = false;
					if (!$a_prodi_request) {
						$b_continue = true;
					}
					else if ((count($a_prodi_request) > 0) AND (in_array($s_study_program_id, $a_prodi_request))) {
						$b_continue = true;
					}

					if ($b_continue) {
						$s_nama_kelas = $mbo_class_iuli_prodi->study_program_abbreviation.$o_class_group->semester_id;
						if (in_array($s_semester_type_id, ['7', '8'])) {
							$s_nama_kelas = $mbo_class_iuli_prodi->study_program_abbreviation.'-'.$o_class_group->semester_type_id;
						}
						$a_data_kelas_kuliah = [
							'id_prodi' => $s_study_program_id,
							'id_semester' => $s_semester_id,
							'id_matkul' => $s_matkul_id,
							'nama_kelas_kuliah' => $s_nama_kelas
						];

						$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
							'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_semester_id}' AND id_matkul = '{$s_matkul_id}' AND nama_kelas_kuliah = '{$s_nama_kelas}'"
						]);

						$delete_kelas = true;
						if(count($a_get_detail_kelas_kuliah->data) > 0) {
							$o_kelas_feeder_data = $a_get_detail_kelas_kuliah->data[0];
							// if (in_array($s_semester_type_id, ['7', '8'])) {
							// 	print('semester pendek belum terfikirkan!!!');exit;
							// }

							$delete_kelas = $this->delete_kelas_kuliah($o_kelas_feeder_data->id_kelas_kuliah, $b_all_sync);
						}

						if ($delete_kelas) {
							$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
								'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_semester_id}' AND id_matkul = '{$s_matkul_id}' AND nama_kelas_kuliah = '{$s_nama_kelas}'"
							]);
	
							if(($a_get_detail_kelas_kuliah->error_code == 0) && (count($a_get_detail_kelas_kuliah->data) == 0)){
								$a_insert_kelas_kuliah = $this->feederapi->post('InsertKelasKuliah', [
									'record' => $a_data_kelas_kuliah
								]);
								if ($a_insert_kelas_kuliah->error_code == '631') {
									// print('<pre>');var_dump($o_class_group);exit;
									$mba_curriculum_data = $this->General->get_where('ref_curriculum', ['curriculum_id' => $o_class_group->curriculum_id]);
									$mba_curriculum_subject_data = $this->General->get_where('ref_curriculum_subject', ['curriculum_subject_id' => $o_class_group->curriculum_subject_id]);
									if (($mba_curriculum_data) AND ($mba_curriculum_subject_data)) {
										$s_id_prodi = ($mba_curriculum_data) ? $mba_curriculum_data[0]->study_program_id : 'xx';
										if ($s_id_prodi != 'xx') {
											$mba_prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_id_prodi]);
											if (($mba_prodi_data) AND (!is_null($mba_prodi_data[0]->study_program_main_id))) {
												$s_id_prodi = $mba_prodi_data[0]->study_program_main_id;
											}
										}
										$s_tahun = ($mba_curriculum_data) ? $mba_curriculum_data[0]->academic_year_id : 'xx';
										$o_get_detail_kurikulum = $this->feederapi->post('GetDetailKurikulum', [
											'filter' => "id_prodi = '$s_id_prodi' AND id_semester LIKE '%$s_tahun%'"
										]);
										
										if(($o_get_detail_kurikulum->error_code == 0) AND (count($o_get_detail_kurikulum->data) > 0)){
											$data = $o_get_detail_kurikulum->data;
											$s_curr_id = $data[0]->id_kurikulum;
											$a_curr_subject_data = [
												'id_kurikulum' => $s_curr_id,
												'id_matkul' => $s_matkul_id,
												'semester' => ($mba_curriculum_subject_data[0]->semester_id > 8) ? 1 : $mba_curriculum_subject_data[0]->semester_id,
												'sks_mata_kuliah' => $mba_curriculum_subject_data[0]->curriculum_subject_credit,
												'apakah_wajib' => ($mba_curriculum_subject_data[0]->curriculum_subject_category == 'mandatory') ? '1' : '2'
											];
											// 
											$a_insert_matkulkurikulum = $this->feederapi->post('InsertMatkulKurikulum', [
												'record' => $a_curr_subject_data
											]);
											if($a_insert_matkulkurikulum->error_code == 0) {
												$a_insert_kelas_kuliah = $this->feederapi->post('InsertKelasKuliah', [
													'record' => $a_data_kelas_kuliah
												]);
											}
											else {
												print_r($a_insert_matkulkurikulum);exit;
											}
										}
										else {
											print('<pre>');var_dump($o_get_detail_kurikulum);
											print('<br>');
											print_r($a_insert_kelas_kuliah);
											exit;
										}
									}
									else {
										print('not found portal, id_matkul: '.$s_matkul_id.' tidak ditemukan kurikulumnya');var_dump($mba_curriculum_subject_data);exit;
									}
								}
								
								if($a_insert_kelas_kuliah->error_code == 0){
									$this->Cgm->save_data([
										'class_group_id' => $a_insert_kelas_kuliah->data->id_kelas_kuliah
									], $o_class_group->class_group_id);
									$s_id_kelas_kuliah = $a_insert_kelas_kuliah->data->id_kelas_kuliah;
	
									$s_message = $o_class_group->class_group_id.' -> '.$a_insert_kelas_kuliah->data->id_kelas_kuliah.'<br>';
									if ($b_all_sync) {
										$this->s_message .= $s_message;
									}
									else {
										print($s_message);
									}
								}
								else{
									print('error sync_kelas_kuliah');
									print_r($a_insert_kelas_kuliah);
									exit;
								}
							}
							else if(count($a_get_detail_kelas_kuliah->data) > 0){
								print('kelas masih ada<br>');
								print('<pre>');var_dump($a_get_detail_kelas_kuliah);
								exit;
							}
	
							if(!is_null($s_id_kelas_kuliah)){
								$this->start_sync_score($s_academic_year_id, $s_semester_type_id, $s_matkul_id, $s_id_kelas_kuliah, $b_all_sync);
								$this->start_sync_class_lecturer($s_id_kelas_kuliah, $b_all_sync);
								// print($o_class_group->class_group_id.' jadi kelas '.$s_id_kelas_kuliah.'; matkul: '.$s_matkul_id.'; prodi: '.$s_study_program_id);
								// print('<br>');
							}else{
								print('id_kelas_kuliah is null:<br>');
								var_dump($o_class_group);
								exit;
							}
						}
					}
				}
				// else{
				// 	if ($o_class_group->curriculum_subject_type != 'extracurricular') {
				// 		print('<pre>');
				// 		print('001<br>');
				// 		var_dump($o_class_group);exit;
				// 	}
				// }
			}
		}else{
			print('credit kosong!');exit;
		}
	}

	public function sync_kurikulum()
	{
		$mba_curriculum = $this->General->get_where('ref_curriculum');
		if ($mba_curriculum) {
			foreach ($mba_curriculum as $o_curriculum) {
				$mba_curriculum_subject = $this->General->get_where('ref_curriculum_subject', [
					'curriculum_id' => $o_curriculum->curriculum_id
				]);

				if ($mba_curriculum_subject) {
					$o_get_detail_kurikulum = $this->feederapi->post('GetDetailKurikulum', [
						'filter' => "nama_kurikulum = '{$o_curriculum->curriculum_name}'"
					]);

					if(($o_get_detail_kurikulum->error_code == 0) AND (count($o_get_detail_kurikulum->data) == 0)){
						
						// if ($s_id_kurikulum != $o_curriculum->curriculum_id) {
						// 	$this->db->update('ref_curriculum', ['curriculum_id' => $s_id_kurikulum], ['curriculum_id' => $o_curriculum->curriculum_id]);
							print('<pre>');var_dump($o_curriculum->curriculum_name);
							print('<br>');
						// }
						
					}
				}
			}
		}
	}

	// public function sync_class_master($s_class_master_id, $b_all_sync = false)
	// {
	// 	$o_class_master = $this->Cgm->get_class_master_data($s_class_master_id)[0];
	// 	$s_academic_year_id = $o_class_master->academic_year_id;
	// 	$s_semester_type_id = $o_class_master->semester_type_id;
	// 	# pisahkan
	// 	// $mba_class_master_groups = $this->Cgm->get_class_master_group(array('class_master_id' => $s_class_master_id));
	// 	// if ($mba_class_master_groups) {
	// 	// 	foreach ($mba_class_master_groups as $o_class_master_groups) {
	// 	// 		$mba_class_subject_overload = $this->Cgm->get_class_by_offered_subject(array('cg.class_group_id' => $o_class_master_groups->class_group_id));
	// 	// 		if (($mba_class_subject_overload) AND (count($mba_class_subject_overload) > 1)) {
	// 	// 			$this->separates_class_group_feeder($o_class_master_groups->class_group_id);
	// 	// 		}
	// 	// 	}
	// 	// }

	// 	// sudah tidak berlaku, jika periode pelaporan sudah full menggunakan portal versi 3 (terbaru)
	// 	# end
	// 	$mba_class_groups = $this->Cgm->get_class_group_filtered([
	// 		'cmc.class_master_id' => $o_class_master->class_master_id
	// 	], true);

	// 	print('<pre>');
	// 	// var_dump($mba_class_groups);
	// 	// exit;

	// 	// $avail = $this->check_semester_bentar($s_class_master_id); //
	// 	$avail = true;
		
	// 	// if($mba_class_groups){
	// 	if($mba_class_groups AND $avail){
	// 		foreach($mba_class_groups as $o_class_group){
	// 			if ($o_class_group->curriculum_subject_credit != 0) {
	// 				if($s_matkul_id = $this->_check_matkul_feeder($o_class_group)){
	// 					// print($o_class_group->class_group_id.'<br>');
	// 					$s_study_program_id = (is_null($o_class_group->class_study_program_main_id)) ? $o_class_group->class_group_study_program_id : $o_class_group->class_study_program_main_id;
	// 					$mbo_class_iuli_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_class_group->class_group_study_program_id])[0];
	// 					$s_semester_id = implode('', [$s_academic_year_id.$s_semester_type_id]);
	// 					if (in_array($s_semester_type_id, ['7', '8'])) {
	// 						$s_semester_id = $s_academic_year_id.'3';
	// 					}

	// 					// sinkron untuk prodi CHE saja
	// 					// if ($s_study_program_id == '6ce5bc8b-10f5-456d-855d-aef18dc641f4') {
	// 						$s_nama_kelas = $mbo_class_iuli_prodi->study_program_abbreviation.str_pad($o_class_group->semester_id, 2, "0", STR_PAD_RIGHT);
	// 						$a_data_kelas_kuliah = [
	// 							'id_prodi' => $s_study_program_id,
	// 							'id_semester' => $s_semester_id,
	// 							'id_matkul' => $s_matkul_id,
	// 							'nama_kelas_kuliah' => $s_nama_kelas
	// 						];
	// 						// print('<pre>');var_dump($a_data_kelas_kuliah);exit;
	// 						// $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
	// 						// 	'filter' => "id_kelas_kuliah = '{$o_class_group->class_group_id}'"
	// 						// ]);
	// 						// // print_r($a_get_detail_kelas_kuliah);
							
	// 						// $s_id_kelas_kuliah = null;
							
	// 						// if(($a_get_detail_kelas_kuliah->error_code == 0) && (count($a_get_detail_kelas_kuliah->data) >= 1)){ // ini harusnya engga, akan ke replace mahasiswa di kelas lamanya (yang beda prodi)
	// 						// 	$s_id_kelas_kuliah = $o_class_group->class_group_id;
	// 						// 	print('1.'.$s_id_kelas_kuliah.'<br>');
	// 						// }
	// 						// else{

	// 							$a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
	// 								'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_semester_id}' AND id_matkul = '{$s_matkul_id}'"
	// 							]);

	// 							// print('<pre>');var_dump($a_get_detail_kelas_kuliah);exit;
		
	// 							if(($a_get_detail_kelas_kuliah->error_code == 0) && (count($a_get_detail_kelas_kuliah->data) == 0)){
	// 								$a_insert_kelas_kuliah = $this->feederapi->post('InsertKelasKuliah', [
	// 									'record' => $a_data_kelas_kuliah
	// 								]);
									
	// 								if($a_insert_kelas_kuliah->error_code == 0){
	// 									$this->Cgm->save_data([
	// 										'class_group_id' => $a_insert_kelas_kuliah->data->id_kls
	// 									], $o_class_group->class_group_id);
	// 									$s_id_kelas_kuliah = $a_insert_kelas_kuliah->data->id_kls;
		
	// 									$s_message = $o_class_group->class_group_id.' -> '.$a_insert_kelas_kuliah->data->id_kls.'<br>';
	// 									if ($b_all_sync) {
	// 										$this->s_message .= $s_message;
	// 									}
	// 									else {
	// 										print($s_message);
	// 									}
	// 								}
	// 								else{
	// 									print_r($a_insert_kelas_kuliah);
	// 									exit;
	// 								}
	// 							}else if(count($a_get_detail_kelas_kuliah->data) > 0){
	// 								$o_kelas_feeder_data = $a_get_detail_kelas_kuliah->data[0];
	// 								// print('<pre>');var_dump($o_kelas_feeder_data);exit;
	// 								$s_id_kelas_kuliah = $o_kelas_feeder_data->id_kelas_kuliah;
	// 								print('2.'.$s_id_kelas_kuliah.'<br>');
	// 								if ($s_id_kelas_kuliah != $o_class_group->class_group_id) {
	// 									$class_group_exists = $this->General->get_where('dt_class_group', ['class_group_id' => $s_id_kelas_kuliah]);
	// 									if ($class_group_exists) {
	// 										// print('<pre>');var_dump($class_group_exists);exit;
											
	// 										// cek prodinya
	// 										// if ($mbo_class_iuli_prodi->study_program_abbreviation == ) {
	// 										// 	# code... SUlit sekaleeeh
	// 										// }

	// 										// if ($class_group_exists[0]->semester_type_id == $s_semester_type_id) { // cek semester akademik
	// 										// 	print('kelas '.$s_id_kelas_kuliah.' sudah ada di feeder');exit;
	// 										// }else{
	// 											$a_data_kelas_kuliah['nama_kelas_kuliah'] = $o_class_group->study_program_abbreviation.$o_class_group->semester_id.'1';
	// 											$a_insert_kelas_kuliah = $this->feederapi->post('InsertKelasKuliah', [
	// 												'record' => $a_data_kelas_kuliah
	// 											]);
												
	// 											if($a_insert_kelas_kuliah->error_code == 0){
	// 												$this->Cgm->save_data([
	// 													'class_group_id' => $a_insert_kelas_kuliah->data->id_kls
	// 												], $o_class_group->class_group_id);
	// 												$s_id_kelas_kuliah = $a_insert_kelas_kuliah->data->id_kls;
					
	// 												$s_message = $o_class_group->class_group_id.' -> '.$a_insert_kelas_kuliah->data->id_kls.'<br>';
	// 												if ($b_all_sync) {
	// 													$this->s_message .= $s_message;
	// 												}
	// 												else {
	// 													print($s_message);
	// 												}
	// 											}
	// 											else{
	// 												print_r($a_insert_kelas_kuliah);
	// 												exit;
	// 											}
	// 										// }
	// 									}else{
	// 										print('error 1512');exit;
	// 										$this->Cgm->save_data([
	// 											'class_group_id' => $s_id_kelas_kuliah
	// 										], $o_class_group->class_group_id);
			
	// 										print($o_class_group->class_group_id.' -> '.$s_id_kelas_kuliah.'<br>');
	// 									}
	// 									// print('Ini beda: class_group_id '.$o_class_group->class_group_id.' != id_kelas_kuliah '.$s_id_kelas_kuliah);exit;
	// 								}
	// 							}
	// 						// }
							
	// 						if(!is_null($s_id_kelas_kuliah)){
	// 							$this->start_sync_score($s_academic_year_id, $s_semester_type_id, $s_matkul_id, $s_id_kelas_kuliah);
	// 							$this->start_sync_class_lecturer($s_id_kelas_kuliah);
	// 							$s_message = $o_class_group->class_group_id.' jadi kelas '.$s_id_kelas_kuliah.'; matkul: '.$s_matkul_id.'; prodi: '.$s_study_program_id.'<br>';
	// 							if ($b_all_sync) {
	// 								$this->s_message .= $s_message;
	// 							}
	// 							else {
	// 								print($s_message);
	// 							}
	// 						}else{
	// 							print('error 1535');
	// 							var_dump($o_class_group);
	// 							exit;
	// 						}
	// 					// }
	// 				}else{
	// 					if ($o_class_group->curriculum_subject_type != 'extracurricular') {
	// 						print('<pre>');
	// 						print('001<br>');
	// 						var_dump($o_class_group);exit;
	// 					}
	// 				}
	// 			}else{
	// 				print('credit kosong!');exit;
	// 			}
	// 		}
	// 	}else{
	// 		print('<pre>');
	// 		print('Class not found!');exit;
	// 	}
	// }
	
	public function start_sync_curriculum_subject($s_curriculum_id, $s_study_program_id)
	{
// 		print "Start syncing subjects: {$s_curriculum_id}\n\n";
		$mba_curriculum_subject = $this->Cm->get_curriculum_subject_list($s_curriculum_id);
		if($mba_curriculum_subject){
			foreach($mba_curriculum_subject as $o_curriculum_subject){
// 				print_r($o_curriculum_subject);
				if(
					(in_array($o_curriculum_subject->semester_id, [1,2,3,4,5,6,7,8])) 
					AND ($o_curriculum_subject->subject_credit != 0)
					AND ($o_curriculum_subject->curriculum_subject_type != 'extracurricular')
				){
					$b_exec_update = true;
					$a_delete_matkul_kurikulum = $this->feederapi->post('DeleteMatkulKurikulum', [
						'key' => [
							'id_kurikulum' => $s_curriculum_id,
							'id_matkul' => $o_curriculum_subject->subject_id
						]
					]);
// 					print_r($a_delete_matkul_kurikulum);
// 					print "Get detail matkul: {$o_curriculum_subject->subject_name}|{$o_curriculum_subject->study_program_abbreviation}\n";
// 					print "id_prodi: {$o_curriculum_subject->study_program_id}\n";
					if($this->get_detail_matkul($o_curriculum_subject->subject_id)){
// 						print "Matkul found: {$o_curriculum_subject->subject_name}|{$o_curriculum_subject->study_program_abbreviation}\n";
						$s_id_matkul = $o_curriculum_subject->subject_id;
					}
					else{
						$o_get_detail_mata_kuliah = $this->feederapi->post('GetDetailMataKuliah', [
							'filter' => "id_prodi = '{$o_curriculum_subject->study_program_id}' AND nama_mata_kuliah = '{$o_curriculum_subject->subject_name}'"
						]);

						if(($o_get_detail_mata_kuliah->error_code == 0) AND (count($o_get_detail_mata_kuliah->data) != 0)){
							$s_id_matkul = $o_get_detail_mata_kuliah->data[0]->id_matkul;
							if($o_curriculum_subject->subject_id != $s_id_matkul){
// 								print "ID is not the same. Sync.\n";
							}
						}
						else{
							$o_insert_mata_kuliah = $this->feederapi->post('InsertMataKuliah', [
								'record' => [
									'id_prodi' => $s_study_program_id,
									'kode_mata_kuliah' => $o_curriculum_subject->subject_code,
									'nama_mata_kuliah' => $o_curriculum_subject->subject_name,
									'id_jenis_mata_kuliah' => $o_curriculum_subject->id_jenis_mata_kuliah,
									'sks_mata_kuliah' => $o_curriculum_subject->subject_credit,
									'sks_tatap_muka' => $o_curriculum_subject->subject_credit_tm,
									'sks_praktek' => $o_curriculum_subject->subject_credit_p,
									'sks_praktek_lapangan' => $o_curriculum_subject->subject_credit_pl,
									'sks_simulasi' => $o_curriculum_subject->subject_credit_s
								]
							]);
// 							print_r($o_insert_mata_kuliah);
							if($o_insert_mata_kuliah->error_code == 0){
								$s_id_matkul = $o_insert_mata_kuliah->data->id_matkul;
								
								if(!$this->Sbj->get_subject_filtered([
									'subject_id' => $s_id_matkul
								])){
									$this->Sbj->save_subject_data([
										'subject_id' => $s_id_matkul
									], $o_curriculum_subject->subject_id);
								}
							}
							else{
// 								print_r($o_insert_mata_kuliah);
								$b_exec_update = false;
								// exit('Error, exiting sync');
							}
						}
					}
					
					if($b_exec_update){
						$o_update_mata_kuliah = $this->feederapi->post('UpdateMataKuliah', [
							'record' => [
								'kode_mata_kuliah' => $o_curriculum_subject->subject_code
							],
							'key' => [
								'id_matkul' => $s_id_matkul
							]
						]);
// 						print "Update matkul: \n";
// 						print_r($o_update_mata_kuliah);
						
// 						print "Get matkul kurikulum: {$o_curriculum_subject->subject_name}|{$o_curriculum_subject->study_program_abbreviation}\n";
						$o_get_matkul_kurikulum = $this->feederapi->post('GetMatkulKurikulum', [
							'filter' => "id_kurikulum = '{$s_curriculum_id}' AND id_matkul = '{$s_id_matkul}'"
						]);
						
						if(($o_get_matkul_kurikulum->error_code == 0) AND (count($o_get_matkul_kurikulum->data) == 0)){
// 							print "Insert matkul kurikulum:\n";
							$a_insert_matkul_kurikulum = $this->feederapi->post('InsertMatkulKurikulum', [
								'record' => [
									'id_kurikulum' => $s_curriculum_id,
									'id_matkul' => $s_id_matkul,
									'semester' => $o_curriculum_subject->semester_id,
									'apakah_wajib' => ($o_curriculum_subject->curriculum_subject_type == 'mandatory') ? 1 : 0
								]
							]);
// 							print_r($a_insert_matkul_kurikulum);
						}
						else{
// 							print "Matkul not found end: {$o_curriculum_subject->subject_name}|{$o_curriculum_subject->study_program_abbreviation}\n";
// 							print_r($o_get_matkul_kurikulum);
						}
					}
					else{
// 						print "Error: \n";
// 						print_r($o_insert_mata_kuliah);
					}
					/*
					if(($a_get_detail_mata_kuliah->error_code == 0) AND (count($a_get_detail_mata_kuliah->data) != 0)){
						
					}*/
// 					print "\n\n";
				}
			}
		}
		else{
// 			print "Oh no, curriculum subjects not found\n\n";
		}
	}
	
	public function start_sync_curriculum($s_academic_year_id = null)
	{
		$a_message = [];
		if(is_null($s_academic_year_id)){
			$s_academic_year_id = $this->input->post('academic_year_id');
		}

		$mba_curriculum_list = $this->Cm->get_curriculum_filtered([
			'academic_year_id' => $s_academic_year_id,
			'study_program_main_id' => NULL
		]);
		
		if($mba_curriculum_list){	
			foreach($mba_curriculum_list as $o_curriculum){
				$a_get_detail_kurikulum = $this->feederapi->post('GetDetailKurikulum', [
					'filter' => "id_prodi='".$o_curriculum->study_program_id."' AND id_semester='{$s_academic_year_id}1'"
				]);
				
				$a_required_curriculum = array(
					'nama_kurikulum' => $o_curriculum->curriculum_name,
					'id_prodi' => $o_curriculum->study_program_id,
					'id_semester' => $s_academic_year_id.'1',
					'jumlah_sks_lulus' => 150,
					'jumlah_sks_wajib' => $o_curriculum->curriculum_total_credit_mandatory,
					'jumlah_sks_pilihan' => array_sum(
						[
							$o_curriculum->curriculum_total_credit_elective,
							$o_curriculum->curriculum_total_credit_extracurricular
						]
					)
				);
				
				if(($a_get_detail_kurikulum->error_code == 0) AND (count($a_get_detail_kurikulum->data) >= 1)){
					if($o_curriculum->curriculum_id != $a_get_detail_kurikulum->data[0]->id_kurikulum){
						$this->Cm->update_curriculum(array(
							'curriculum_id' => $a_get_detail_kurikulum->data[0]->id_kurikulum
						), $o_curriculum->curriculum_id);
					}
				}else if(($a_get_detail_kurikulum->error_code == 0) AND (count($a_get_detail_kurikulum->data) == 0)) {
					$o_curriculum_result = $this->feederapi->post('InsertKurikulum', array(
						'record' => $a_required_curriculum
					));
					
					if($o_curriculum_result->error_code == 0){
						$s_feeder_curriculum_id = $o_curriculum_result->data->id_kurikulum;
						$a_update_curriculum = $this->Cm->update_curriculum(array(
							'curriculum_id' => $s_feeder_curriculum_id
						), $o_curriculum->curriculum_id);
					}
					else{
						print('<pre>');
						print('error 003<br>');
						var_dump($o_curriculum_result);exit;
					}
				}else{
					print('<pre>');
					print('error 002<br>');
					var_dump($a_get_detail_kurikulum);exit;
				}
				$this->start_sync_curriculum_subject($o_curriculum->curriculum_id, $o_curriculum->study_program_id);
			}
			print json_encode(['code' => 0]);
			exit;
		}
		else{
			print "Curriculum not found:\n";
		}
	}

	public function separates_class_group_feeder($s_class_group_id)
    {
        $mba_class_offered_subject = $this->Cgm->get_class_by_offered_subject(array('cg.class_group_id' => $s_class_group_id));
        $mba_class_group_lecturer = $this->Cgm->get_class_group_lecturer(array('cgl.class_group_id' => $s_class_group_id));
        $mbo_class_group_master = $this->Cgm->get_class_id_master_class(array('class_group_id' => $s_class_group_id))[0];
        // print('<pre>');
        // var_dump($mba_class_offered_subject);exit;
        if ($mba_class_offered_subject) {
            $i = 1;
            $this->db->trans_start();
            $a_class_study_prog = array();

            foreach ($mba_class_offered_subject as $o_class_subject) {
                if ($i < count($mba_class_offered_subject)) {
                    $_s_class_group_id = $this->uuid->v4();
                    $a_class_group_data = array(
                        'class_group_id' => $_s_class_group_id,
                        'academic_year_id' => $o_class_subject->academic_year_id,
                        'semester_type_id' => $o_class_subject->semester_type_id,
                        'class_group_name' => $o_class_subject->subject_name.' '.$o_class_subject->study_program_abbreviation,
                        'date_added' => $o_class_subject->date_added
                    );

                    $b_save_class_group = $this->Cgm->save_data($a_class_group_data);
                    if ($b_save_class_group) {
                        if (!array_key_exists($o_class_subject->study_program_id, $a_class_study_prog)) {
                            $a_class_study_prog[$o_class_subject->study_program_id] = $_s_class_group_id;
                        }
                        if ($mba_class_group_lecturer) {
                            foreach ($mba_class_group_lecturer as $o_class_lecturer) {
                                $a_class_group_lecturer_data = array(
                                    'class_group_lecturer_id' => $this->uuid->v4(),
                                    'class_group_id' => $_s_class_group_id,
                                    'employee_id' => $o_class_lecturer->employee_id,
                                    'employee_id_reported' => $o_class_lecturer->employee_id_reported,
                                    'credit_allocation' => $o_class_lecturer->credit_allocation,
                                    'credit_charged' => $o_class_lecturer->credit_charged,
                                    'credit_realization' => $o_class_lecturer->credit_realization,
                                    'class_group_lecturer_status' => $o_class_lecturer->class_group_lecturer_status,
                                    'class_group_lecturer_preferable_day' => $o_class_lecturer->class_group_lecturer_preferable_day,
                                    'class_group_lecturer_preferable_time' => $o_class_lecturer->class_group_lecturer_preferable_time,
                                    'class_group_lecturer_priority' => $o_class_lecturer->class_group_lecturer_priority,
                                    'is_reported_to_feeder' => $o_class_lecturer->is_reported_to_feeder,
                                    'date_added' => $o_class_lecturer->date_added,
                                );
                                $b_save_class_group_lecturer = $this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
                            }
                        }
                        $a_class_group_subject_update_data = array(
                            'class_group_id' => $_s_class_group_id
                        );

                        $b_update_class_subject_data = $this->Cgm->save_class_group_subject($a_class_group_subject_update_data, $o_class_subject->class_group_subject_id);
                        if ($b_update_class_subject_data) {
                            $a_class_master_class_data = array(
                                'class_master_id' => $mbo_class_group_master->class_master_id,
                                'class_group_id' => $_s_class_group_id
                            );

                            $this->Cgm->save_class_master_class($a_class_master_class_data);
                        }
                    }
                }
                $i++;
            }

            if (count($a_class_study_prog)) {
                foreach ($a_class_study_prog as $key => $value) {
                    $mba_score_data = $this->Scm->get_score_data(array('class_group_id' => $s_class_group_id));
                    if ($mba_score_data) {
                        foreach ($mba_score_data as $o_score) {
                            if ($o_score->student_study_program_id == $key) {
                                // update class_group_id score where score_id=$i_score->score_id AND class_group_id = $s_class_group_id
                                $this->Scm->save_data(array('class_group_id' => $value), array('score_id' => $o_score->score_id, 'class_group_id' => $s_class_group_id));
                            }
                        }
                    }
                }
            }
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
            }
        }
    }
	
	public function start_sync($s_academic_year, $s_semester_type)
	{
		print "<pre>";
		$this->start_sync_curriculum();
		print "</pre>";
	}

	function feeder_sync_class($s_semester_period) {
        $s_academic_year_id = substr($s_semester_period, 0, 4);
        $s_semester_type_id = substr($s_semester_period, 4);
        
        $mba_class_master_data = $this->General->get_where('dt_class_master', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id], [
			'class_master_name' => 'ASC'
		]);
        if ($mba_class_master_data) {
            foreach ($mba_class_master_data as $o_class_master) {
                $sync_class = $this->feeder_sync_class_master($o_class_master->class_master_id);
            }
        }
        // print('<pre>');var_dump($s_academic_year_id);exit;
    }

    function feeder_sync_class_master($s_class_master_id) {
        $mba_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id]);
        if ($mba_class_master_data) {
            $o_class_master = $mba_class_master_data[0];
            $mba_class_score = $this->General->get_where('dt_score', [
                'class_master_id' => $o_class_master->class_master_id,
                'score_approval' => 'approved'
            ]);
			print('<p><===================================================================></p>');
            print("Syncronize class $o_class_master->class_master_name ($o_class_master->running_year/$o_class_master->class_semester_type_id)");
            print('<br>');
            if ($mba_class_score) {
                $a_class_group_id = [];
                foreach ($mba_class_score as $o_score) {
                    if ((!is_null($o_score->class_group_id)) AND (!in_array($o_score->class_group_id, $a_class_group_id))) {
                        array_push($a_class_group_id, $o_score->class_group_id);
                    }
                }

                foreach ($a_class_group_id as $s_class_group_id) {
                    $sync_class_group = $this->feeder_sync_class_group($s_class_group_id);
                }
            }
            else {
                print("member student not found in class $o_class_master->class_master_name !");print('<br>');
            }
        }
        else {
            print('class master '.$s_class_master_id.' not found!');print('<br>');
        }
    }

    function feeder_sync_class_group($s_class_group_id) {
        $mba_class_groups = $this->Cgm->get_class_group_filtered([
            'dcg.class_group_id' => $s_class_group_id
        ], true);
        if ($mba_class_groups) {
            $o_class_group = $mba_class_groups[0];
			print('<p><----------------------------------------------------------------------------></p>');
            print("Syncronize sub class $o_class_group->class_group_name ($o_class_group->running_year/$o_class_group->class_semester_type_id)");
            print('<br>');

            if ($o_class_group->curriculum_subject_credit != 0) {
                if ($o_class_group->curriculum_subject_type != 'extracurricular') {
                    $s_subject_id = $o_class_group->subject_id;
                    $s_study_program_id = (is_null($o_class_group->class_study_program_main_id)) ? $o_class_group->class_group_study_program_id : $o_class_group->class_study_program_main_id;
                    $mbo_class_iuli_prodi = $this->General->get_where('ref_study_program', ['study_program_id' => $o_class_group->class_group_study_program_id])[0];
                    $s_periode_semester = (in_array($o_class_group->class_semester_type_id, ['7', '8'])) ? $o_class_group->running_year.'3' : $o_class_group->running_year.$o_class_group->class_semester_type_id;
                    $s_nama_kelas = $mbo_class_iuli_prodi->study_program_abbreviation.$o_class_group->semester_id;
                    if (in_array($o_class_group->class_semester_type_id, ['7', '8'])) {
                        $s_nama_kelas = $mbo_class_iuli_prodi->study_program_abbreviation.'-'.$o_class_group->class_semester_type_id;
                    }
                    
                    $a_data_kelas_kuliah = [
                        'id_prodi' => $s_study_program_id,
                        'id_semester' => $s_periode_semester,
                        'id_matkul' => $s_subject_id,
                        'nama_kelas_kuliah' => $s_nama_kelas
                    ];

                    $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
                        'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_periode_semester}' AND id_matkul = '{$s_subject_id}' AND nama_kelas_kuliah = '{$s_nama_kelas}'"
                    ]);

                    $delete_kelas = true;
                    if(count($a_get_detail_kelas_kuliah->data) > 0) {
                        $o_kelas_feeder_data = $a_get_detail_kelas_kuliah->data[0];
                        $delete_kelas = $this->delete_kelas_kuliah($o_kelas_feeder_data->id_kelas_kuliah);
                    }

                    if ($delete_kelas) {
                        $a_get_detail_kelas_kuliah = $this->feederapi->post('GetDetailKelasKuliah', [
                            'filter' => "id_prodi = '{$s_study_program_id}' AND id_semester = '{$s_periode_semester}' AND id_matkul = '{$s_subject_id}' AND nama_kelas_kuliah = '{$s_nama_kelas}'"
                        ]);

                        if (($a_get_detail_kelas_kuliah->error_code == 0) && (count($a_get_detail_kelas_kuliah->data) == 0)) {
                            $a_insert_kelas_kuliah = $this->feederapi->post('InsertKelasKuliah', [
                                'record' => $a_data_kelas_kuliah
                            ]);

                            if($a_insert_kelas_kuliah->error_code == 0){
                                $this->Cgm->save_data([
                                    'class_group_id' => $a_insert_kelas_kuliah->data->id_kelas_kuliah
                                ], $o_class_group->class_group_id);
                                $s_id_kelas_kuliah = $a_insert_kelas_kuliah->data->id_kelas_kuliah;
                                print($o_class_group->class_group_id.' berubah menjadi '.$a_insert_kelas_kuliah->data->id_kelas_kuliah.'<br>');

								if(!is_null($s_id_kelas_kuliah)){
									$this->start_sync_score($o_class_group->running_year, $o_class_group->class_semester_type_id, $s_subject_id, $s_id_kelas_kuliah);
									$this->start_sync_class_lecturer($s_id_kelas_kuliah);
									// print($o_class_group->class_group_id.' jadi kelas '.$s_id_kelas_kuliah.'; matkul: '.$s_matkul_id.'; prodi: '.$s_study_program_id);
									// print('<br>');
								}else{
									print('id_kelas_kuliah is null:<br>');
									var_dump($o_class_group);
									// exit;
								}
                            }
                            else{
                                print('error InsertKelasKuliah');
                                print_r($a_insert_kelas_kuliah);
                                // exit;
                            }
                        }
                        else {
                            print('kelas '.$s_nama_kelas.' tidak berhasil dihapus dari feeder!');print('<br>');
                        }
                    }
                }
            }
        }
        else {
            print('class group '.$s_class_group_id.' not found!');print('<br>');
        }
    }
}