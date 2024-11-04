<style>
    table.table, table.table td, table.table th {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 2px;
        font-size: 11px;
    }
</style>
<div style="width: 100%; text-align: center;">
    <h4>STUDENT ABSENCE</h4>
</div>

<table border="0">
    <tr>
        <td>Subject</td>
        <td>: <?= $o_class_master_data->subject_name;?></td>
    </tr>
    <tr>
        <td>Study Program</td>
        <td>: <?= $o_study_program->study_program_name;?></td>
    </tr>
    <!-- <tr><td><br></td><td></td></tr> -->
    <tr>
        <td>Lecturer</td>
        <td>: <?= implode(', ', $a_lecturer_name)?></td>
    </tr>
</table>
<br>
<?php
$i_max_column = 16;
$i_max_index = $i_max_column;
$index = 0;
$i_total_absence = ($mba_uosd) ? count($mba_uosd) : 0;
// $i_count_table = ($mba_uosd) ? round(($i_total_absence / $i_max_index), 0, PHP_ROUND_HALF_UP) : 1;
$i_count_table = ($mba_uosd) ? ((($i_total_absence % $i_max_index) > 0) ? (intval($i_total_absence/$i_max_index) + 1) : intval($i_total_absence/$i_max_index)) : 1;
// print($i_count_table);exit;

for ($i=0; $i < $i_count_table; $i++) { 
?>
<table class="table" style="margin-top: 30px;">
    <tr>
        <th width="145px">Student Name</th>
        <th width="70px">Student Number</th>
        <?php
    if ($mba_uosd) {
        $index_header = $index;
        $i_numb = $index_header + 1;
        // foreach ($mba_uosd as $o_uosd) {
        for ($x=$index_header; $x < $i_max_index; $x++) {
            $s_code = $o_study_program->study_program_abbreviation.($i_numb++);
            if ($x < count($mba_uosd)) {
?>
        <th align="center" width="45px">
            <?=$s_code;?>
        </th>
<?php
                $index_header++;
            }
        }
    }
?>
    </tr>
<?php
    if ($mba_score) {
        foreach ($mba_score as $o_score) {
            // if ($o_score->student_status == 'active') {
?>
    <tr>
        <td><?=$o_score->personal_data_name;?></td>
        <td><?=$o_score->student_number;?></td>
<?php
            $a_absence = (count($o_score->absence_data) > 0) ? $o_score->absence_data : $mba_uosd;
            $index_body = $index;
            // foreach ($a_absence as $o_absence) {
            for ($x=$index; $x < $i_max_index; $x++) {
                if ($mba_uosd) {
                    if ($x < count($mba_uosd)) {
                        $s_absence = (count($o_score->absence_data) > 0) ? $a_absence[$x][0]->absence_status : 'PRESENT';
?>
        <td align="center"><?= $key_absence[$s_absence]; ?></td>
<?php
                        // $index_body++;
                    }
                }
            }
?>
    </tr>
<?php
            // }
        }

        $index = $i_max_index;
        $i_max_index += $i_max_column;
    }
?>
</table>
<?php
}
?>

<p style="margin-top: 35px;"><br></p>