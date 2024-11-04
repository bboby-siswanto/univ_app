<h5>PANGKALAN DATA PENDIDIKAN TINGGI <br>
KEMENTRIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h5>
<hr>
<center>
<h3>DAFTAR STATUS MAHASISWA</h3>
<p>
    data per <?=date('d F Y');?><br><small>sumber: NeoFeeder</small>
</p>
</center>
<?php
    $arrdata = [];
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
            $arrdata['aktif'.$i.'1'] = 0;
            $arrdata['aktif'.$i.'2'] = 0;
            $arrdata['merdeka'.$i.'1'] = 0;
            $arrdata['merdeka'.$i.'2'] = 0;
            $arrdata['nonaktif'.$i.'1'] = 0;
            $arrdata['nonaktif'.$i.'2'] = 0;
            $arrdata['cuti'.$i.'1'] = 0;
            $arrdata['cuti'.$i.'2'] = 0;
            $arrdata['resign'.$i.'1'] = 0;
            $arrdata['resign'.$i.'2'] = 0;
            $arrdata['dropout'.$i.'1'] = 0;
            $arrdata['dropout'.$i.'2'] = 0;
            $arrdata['lulus'.$i.'1'] = 0;
            $arrdata['lulus'.$i.'2'] = 0;
            $arrdata['allcount'.$i.'1'] = 0;
            $arrdata['allcount'.$i.'2'] = 0;
        }
    }
?>
<table id="list_student_table" class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Student Name</th>
            <th>Student ID</th>
            <th>Periode Masuk</th>
            <th>Study Program</th>
            <th>Jenis Pendaftaran</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$i;?>1</th>
            <th><?=$i;?>2</th>
    <?php
        }
    }
    ?>
        </tr>
    </thead>
    <tbody>
<?php
if ($list_data) {
    $i_no = 1;
    // print('<pre>');var_dump($list_data);exit;
    foreach ($list_data as $o_data) {
    ?>
        <tr>
            <td><?=$i_no++;?></td>
            <td><?=$o_data['fullname'];?></td>
            <td><?=$o_data['student_number'];?></td>
            <td><?=$o_data['periode_masuk'];?></td>
            <td><?=$o_data['prodi'];?></td>
            <td><?=$o_data['jenis_daftar'];?></td>
            <!-- <td></td> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) {
            $key_odd = $i.'1';
            $key_even = $i.'2';
            $s_keyodd_value = (array_key_exists($key_odd, $o_data)) ? $o_data[$key_odd] : '-';
            $s_keyeven_value = (array_key_exists($key_even, $o_data)) ? $o_data[$key_even] : '-';

            print('<td>'.$s_keyodd_value.'</td>');
            print('<td>'.$s_keyeven_value.'</td>');

            if ($s_keyodd_value == 'A') {
                $arrdata['aktif'.$i.'1']++;
            }
            elseif ($s_keyodd_value == 'M') {
                $arrdata['merdeka'.$i.'1']++;
            }
            elseif ($s_keyodd_value == 'N') {
                $arrdata['nonaktif'.$i.'1']++;
            }
            elseif ($s_keyodd_value == 'C') {
                $arrdata['cuti'.$i.'1']++;
            }
            elseif ($s_keyodd_value == 'R') {
                $arrdata['resign'.$i.'1']++;
            }
            elseif ($s_keyodd_value == 'DO') {
                $arrdata['dropout'.$i.'1']++;
            }
            elseif ($s_keyodd_value == 'L') {
                $arrdata['lulus'.$i.'1']++;
            }

            if ($s_keyeven_value == 'A') {
                $arrdata['aktif'.$i.'2']++;
            }
            elseif ($s_keyeven_value == 'M') {
                $arrdata['merdeka'.$i.'2']++;
            }
            elseif ($s_keyeven_value == 'N') {
                $arrdata['nonaktif'.$i.'2']++;
            }
            elseif ($s_keyeven_value == 'C') {
                $arrdata['cuti'.$i.'2']++;
            }
            elseif ($s_keyeven_value == 'R') {
                $arrdata['resign'.$i.'2']++;
            }
            elseif ($s_keyeven_value == 'DO') {
                $arrdata['dropout'.$i.'2']++;
            }
            elseif ($s_keyeven_value == 'L') {
                $arrdata['lulus'.$i.'2']++;
            }

            $arrdata['allcount'.$i.'1'] = $arrdata['aktif'.$i.'1'] + $arrdata['merdeka'.$i.'1'] + $arrdata['nonaktif'.$i.'1'] + $arrdata['cuti'.$i.'1'] + $arrdata['resign'.$i.'1'] + $arrdata['dropout'.$i.'1'] + $arrdata['lulus'.$i.'1'];
            $arrdata['allcount'.$i.'2'] = $arrdata['aktif'.$i.'2'] + $arrdata['merdeka'.$i.'2'] + $arrdata['nonaktif'.$i.'2'] + $arrdata['cuti'.$i.'2'] + $arrdata['resign'.$i.'2'] + $arrdata['dropout'.$i.'2'] + $arrdata['lulus'.$i.'2'];
        }
    }
    ?>
        </tr>
    <?php
    }
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total Aktif</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['aktif'.$i.'1'];?></th>
            <th><?=$arrdata['aktif'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Total Aktif (Kampus Merdeka)</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['merdeka'.$i.'1'];?></th>
            <th><?=$arrdata['merdeka'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Total Non Aktif</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['nonaktif'.$i.'1'];?></th>
            <th><?=$arrdata['nonaktif'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Total Cuti</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['cuti'.$i.'1'];?></th>
            <th><?=$arrdata['cuti'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Total Resign</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['resign'.$i.'1'];?></th>
            <th><?=$arrdata['resign'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Total Drop Out</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['dropout'.$i.'1'];?></th>
            <th><?=$arrdata['dropout'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Total Lulus</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['lulus'.$i.'1'];?></th>
            <th><?=$arrdata['lulus'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
        <tr>
            <th colspan="6">Jumlah</th>
            <!-- <th>Current Status</th> -->
    <?php
    if ($start_year_header) {
        for ($i=$start_year_header; $i <= $end_year_header; $i++) { 
    ?>
            <th><?=$arrdata['allcount'.$i.'1'];?></th>
            <th><?=$arrdata['allcount'.$i.'2'];?></th>
    <?php
        }
    }
    ?>
        </tr>
    </tfoot>
</table>

<script>
    var tabledata = $('table#list_student_table').DataTable({
        ordering: false,
        searching: false,
        paging: false,
        info: false
    });
</script>