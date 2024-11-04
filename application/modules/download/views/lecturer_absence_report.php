<style>
    table.table, table.table td, table.table th {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<div style="width: 100%; text-align: center;">
    <h4>LECTURER ATTENDANCE REPORT</h4>
</div>
<p></p>
<table border="0">
    <tr>
        <td>Lecturer Name</td>
        <td>: <?= implode(', ', $a_lecturer_name)?></td>
    </tr>
    <tr>
        <td>Subject</td>
        <td>: <?= $o_class_master_data->subject_name;?></td>
    </tr>
    <tr>
        <td>Study Program</td>
        <td>: <?=$o_study_program->study_program_name;?></td>
    </tr>
    <tr>
        <td>Semester</td>
        <td>: <?= $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id?></td>
    </tr>
</table>
<p></p>
<table style="width: 100%" class="table">
    <tr>
        <?php if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        ?>
        <td width="7%">Code</td>
        <?php
        }
        ?>
        <td width="20%">Start Time</td>
        <td width="20%">End Time</td>
        <td>Topics Covered</td>
    </tr>
<?php
if ($mba_uosd) {
    $i_numb = 1;
    foreach ($mba_uosd as $o_uosd) {
        $s_code = $o_study_program->study_program_abbreviation.($i_numb++);
?>
    <tr>
        <?php if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        ?>
        <td><?=$s_code;?></td>
        <?php
        }
        ?>
        <td><?= date('d M Y H:i', strtotime($o_uosd->subject_delivered_time_start));?></td>
        <td><?= date('d M Y H:i', strtotime($o_uosd->subject_delivered_time_end)); ?></td>
        <td><?= str_replace('&amp;', ' and ', $o_uosd->subject_delivered_description); ?></td>
    </tr>
<?php
    }
}
?>
</table>
<div>
    <p></p>
    <div style="float: left; width: 33%;">
        Received By
        <p></p><p></p><p></p>
        <u><?= ''//$s_deans; ?>Chandra Hendrianto</u><br>
        Head of Academic Service Centre
    </div>
    <div style="width: 34%;">
        Knowledge By
        <p></p><p></p><p></p>
        <u><?= ''//$s_deans; ?></u><br>
        Head of Study Program
    </div>
    <div style="">
        Prepared by
        <p></p><p></p><p></p>
        <u><?= implode(' & ', $a_lecturer_name); ?></u>
        <br>
        Lecturer
    </div>
</div>