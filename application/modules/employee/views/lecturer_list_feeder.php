<div class="card">
    <div class="card-body">
        <h5>Daftar Dosen Universitas Lintas Internasional Indonesia</h5>
        <table class="table table-hover" id="table_list">
            <thead>
                <tr class="bg-dark">
                    <th>Nama Dosen</th>
                    <th>NIDN</th>
                    <th>Jenis Kelamin</th>
                    <th>Agama</th>
                    <th>Status Dosen</th>
                </tr>
            </thead>
            <tbody>
        <?php
        if ((isset($lecturer_list)) AND ($lecturer_list)) {
            foreach ($lecturer_list as $o_lecturer) {
        ?>
                <tr>
                    <td>
                        <?= ($o_lecturer->iuli_exists) ? '<a href="'.base_url().'employee/staff_data/'.$o_lecturer->id_dosen.'" target="_blank">'.$o_lecturer->nama_dosen.'</a>' : $o_lecturer->nama_dosen; ?>
                    </td>
                    <td>
                        <?=$o_lecturer->nidn;?>
                    </td>
                    <td>
                        <?=($o_lecturer->jenis_kelamin == 'L') ? 'Laki - Laki' : 'Perempuan';?>
                    </td>
                    <td>
                        <?=$o_lecturer->nama_agama;?>
                    </td>
                    <td>
                        <?=$o_lecturer->nama_status_aktif;?>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
            </tbody>
        </table>
    </div>
</div>
<script>
let table_list = $('#table_list').DataTable();
</script>