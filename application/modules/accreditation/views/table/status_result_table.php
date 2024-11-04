<table id="result_student_table" class="table table-sm table-bordered">
    <thead>
        <tr>
            <th rowspan="2">Tahun Akademik</th>
            <th colspan="6">Jumlah Mahasiswa</th>
            <th rowspan="2">Total</th>
        </tr>
        <tr>
            <th>Aktif</th>
            <th>Non Aktif</th>
            <th>Cuti</th>
            <th>Resign</th>
            <th>Drop Out</th>
            <th>Lulus</th>
        </tr>
    </thead>
    <tbody>
<?php
// print('<pre>');var_dump($list_status);exit;
if ((isset($list_status)) AND (is_array($list_status)) AND (count($list_status) > 0)) {
    for ($i=$start_year_header; $i <= $end_year_header ; $i++) { 
        $dtotal = intval($list_status[$i.'1']['aktif']) + intval($list_status[$i.'1']['non aktif']) + intval($list_status[$i.'1']['cuti']);
        $dtotal += intval($list_status[$i.'1']['resign']) + intval($list_status[$i.'1']['drop out']) + intval($list_status[$i.'1']['lulus']);
?>
        <tr>
            <td><?=$i.'/'.($i+1);?></td>
            <td>
                <?=$list_status[$i.'1']['aktif'];?>
            </td>
            <td><?=$list_status[$i.'1']['non aktif'];?></td>
            <td><?=$list_status[$i.'1']['cuti'];?></td>
            <td><?=$list_status[$i.'1']['resign'];?></td>
            <td><?=$list_status[$i.'1']['drop out'];?></td>
            <td><?=$list_status[$i.'1']['lulus'];?></td>
            <td><?=$dtotal;?></td>
        </tr>
<?php
    }
}
?>
    </tbody>
</table>