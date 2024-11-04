<div class="card">
    <div class="card-body row">
        <div class="col-md-4">
        <?php
                if($a_avatar){
            ?>
                <img src="<?=site_url('file_manager/view/'.$a_avatar[0]->document_id.'/'.$a_avatar[0]->personal_data_id)?>" class="img-fluid img-thumbnail" style="max-height: 300px !important">
            <?php
                }else{
            ?>
                <img src="<?=base_url()?>assets/img/silhouette.png" class="img-fluid img-thumbnail" style="max-height: 300px !important">
            <?php
                }
            ?>
        </div>
        <div class="col-md-8 pt-2">
            <table>
                <tr>
                    <td><?=($biodata) ? $biodata->nama_dosen : '';?></td>
                </tr>
                <tr>
                    <td><?=($biodata) ? $biodata->nidn : '';?></td>
                </tr>
                <tr>
                    <td><?=($is_deans) ? 'Dekan '.$is_deans[0]->faculty_name_feeder : '';?></td>
                </tr>
                <tr>
                    <td><?=($is_hod) ? 'Ketua Program Studi '.$is_hod[0]->study_program_name_feeder : '';?></td>
                </tr>
                <tr>
                    <td>program study</td>
                </tr>
                <tr>
                    <td><?=($biodata) ? ($biodata->jenis_kelamin == 'L') ? 'Laki-Laki' : 'Perempuan' : '';?></td>
                </tr>
                <tr>
                    <td><?=($biodata) ? $biodata->nama_status_aktif : '';?></td>
                </tr>
            </table>
        </div>
        <hr>
        <div class="col-12 mt-4">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-penugasan-tab" data-toggle="tab" data-target="#nav-penugasan" type="button" role="tab" aria-controls="nav-penugasan" aria-selected="true">List Penugasan</button>
                    <button class="nav-link" id="nav-fungsional-tab" data-toggle="tab" data-target="#nav-fungsional" type="button" role="tab" aria-controls="nav-fungsional" aria-selected="false">Riwayat Fungsional</button>
                    <button class="nav-link" id="nav-pangkat-tab" data-toggle="tab" data-target="#nav-pangkat" type="button" role="tab" aria-controls="nav-pangkat" aria-selected="false">Riwayat Pangkat</button>
                    <button class="nav-link" id="nav-pendidikan-tab" data-toggle="tab" data-target="#nav-pendidikan" type="button" role="tab" aria-controls="nav-pendidikan" aria-selected="false">Riwayat Pendidikan</button>
                    <button class="nav-link" id="nav-sertifikasi-tab" data-toggle="tab" data-target="#nav-sertifikasi" type="button" role="tab" aria-controls="nav-sertifikasi" aria-selected="false">Riwayat Sertifikasi</button>
                    <button class="nav-link" id="nav-penelitian-tab" data-toggle="tab" data-target="#nav-penelitian" type="button" role="tab" aria-controls="nav-penelitian" aria-selected="false">Riwayat Penelitian</button>
                    <button class="nav-link" id="nav-pengajaran-tab" data-toggle="tab" data-target="#nav-pengajaran" type="button" role="tab" aria-controls="nav-pengajaran" aria-selected="false">Riwayat Pengajaran</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-penugasan" role="tabpanel" aria-labelledby="nav-penugasan-tab">
                    <table class="table table-hover" id="table_penugasan">
                        <thead>
                            <tr class="bg-dark">
                                <th>Tahun Ajaran</th>
                                <th>Program Studi</th>
                                <th>Nomor Surat Tugas</th>
                                <th>Tanggal Surat Tugas</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if ($penugasan) {
                    foreach ($penugasan as $o_penugasan) {
                ?>
                            <tr>
                                <td><?=$o_penugasan->nama_tahun_ajaran;?></td>
                                <td><?=$o_penugasan->nama_program_studi;?></td>
                                <td><?=$o_penugasan->nomor_surat_tugas;?></td>
                                <td><?=$o_penugasan->tanggal_surat_tugas;?></td>
                            </tr>
                <?php
                    }
                }
                ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-fungsional" role="tabpanel" aria-labelledby="nav-fungsional-tab">
                    <table class="table table-hover" id="table_fungsional">
                        <thead>
                            <tr class="bg-dark">
                                <th>Nama Jabatan Fungsional</th>
                                <th>SK Jabatan Fungsional</th>
                                <th>Tanggal Mulai SK</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if ($fungsional) {
                    foreach ($fungsional as $o_fungsional) {
                ?>
                            <tr>
                                <td><?=$o_fungsional->nama_jabatan_fungsional;?></td>
                                <td><?=$o_fungsional->sk_jabatan_fungsional;?></td>
                                <td><?=$o_fungsional->mulai_sk_jabatan;?></td>
                            </tr>
                <?php
                    }
                }
                ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-pangkat" role="tabpanel" aria-labelledby="nav-pangkat-tab">
                    <table class="table table-hover" id="table_pangkat">
                        <thead>
                            <tr class="bg-dark">
                                <th>Nama Pangkat Golongan</th>
                                <th>SK Pangkat</th>
                                <th>Tanggal SK</th>
                                <th>Masa Kerja</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if ($pangkat) {
                    foreach ($pangkat as $o_pangkat) {
                ?>
                            <tr>
                                <td><?=$o_pangkat->nama_pangkat_golongan;?></td>
                                <td><?=$o_pangkat->sk_pangkat;?></td>
                                <td><?=$o_pangkat->tanggal_sk_pangkat;?></td>
                                <td><?=$o_pangkat->masa_kerja_dalam_tahun.' Tahun '.$o_pangkat->masa_kerja_dalam_bulan.' Bulan';?></td>
                            </tr>
                <?php
                    }
                }
                ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-pendidikan" role="tabpanel" aria-labelledby="nav-pendidikan-tab">
                    <table class="table table-hover" id="table_pendidikan">
                        <thead>
                            <tr class="bg-dark">
                                <th>Bidang Studi</th>
                                <th>Jenjang Pendidikan</th>
                                <th>Gelar Akademik</th>
                                <th>Perguruan Tinggi</th>
                                <th>Fakultas</th>
                                <th>Tahun Lulus</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if ($pendidikan) {
                    foreach ($pendidikan as $o_pendidikan) {
                ?>
                            <tr>
                                <td><?=$o_pendidikan->nama_bidang_studi;?></td>
                                <td><?=$o_pendidikan->nama_jenjang_pendidikan;?></td>
                                <td><?=$o_pendidikan->nama_gelar_akademik;?></td>
                                <td><?=$o_pendidikan->nama_perguruan_tinggi;?></td>
                                <td><?=$o_pendidikan->fakultas;?></td>
                                <td><?=$o_pendidikan->tahun_lulus;?></td>
                            </tr>
                <?php
                    }
                }
                ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-sertifikasi" role="tabpanel" aria-labelledby="nav-sertifikasi-tab">
                    <table class="table table-hover" id="table_sertifikasi">
                        <thead>
                            <tr class="bg-dark">
                                <th>Nomor Peserta</th>
                                <th>Bidang Studi</th>
                                <th>Jenis Sertifikasi</th>
                                <th>Tahun Sertifikasi</th>
                                <th>SK Sertifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if ($sertifikasi) {
                    foreach ($sertifikasi as $o_sertifikasi) {
                ?>
                            <tr>
                                <td><?=$o_sertifikasi->nomor_peserta;?></td>
                                <td><?=$o_sertifikasi->nama_bidang_studi;?></td>
                                <td><?=$o_sertifikasi->nama_jenis_sertifikasi;?></td>
                                <td><?=$o_sertifikasi->tahun_sertifikasi;?></td>
                                <td><?=$o_sertifikasi->sk_sertifikasi;?></td>
                            </tr>
                <?php
                    }
                }
                ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-penelitian" role="tabpanel" aria-labelledby="nav-penelitian-tab">
                    <table class="table table-hover" id="table_penelitian">
                        <thead>
                            <tr class="bg-dark">
                                <th>Judul Penelitian</th>
                                <th>Kelompok Bidang</th>
                                <th>Lembaga IPTEK</th>
                                <th>Tahun Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if ($penelitian) {
                    foreach ($penelitian as $o_penelitian) {
                ?>
                            <tr>
                                <td><?=$o_penelitian->judul_penelitian;?></td>
                                <td><?=$o_penelitian->nama_kelompok_bidang;?></td>
                                <td><?=$o_penelitian->nama_lembaga_iptek;?></td>
                                <td><?=$o_penelitian->tahun_kegiatan;?></td>
                            </tr>
                <?php
                    }
                }
                ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-pengajaran" role="tabpanel" aria-labelledby="nav-pengajaran-tab">...</div>
            </div>
        </div>
    </div>
</div>
<script>
$("#table_penugasan, #table_fungsional, #table_pangkat, #table_pendidikan, #table_sertifikasi, #table_penelitian").DataTable({
    ordering: false,
    searching: false,
    paging: false,
    info: false
})
</script>