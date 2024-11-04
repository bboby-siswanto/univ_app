<style>
    table.table, table.table td, table.table th {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<table border="0">
    <tr><td><br></td><td></td></tr>
    <tr>
        <td>Student Name</td>
        <td>: <?= $o_score->personal_data_name?></td>
    </tr>
    <tr>
        <td>Student Number</td>
        <td>: <?= $o_score->student_number;?></td>
    </tr>
    <tr>
        <td>Batch</td>
        <td>: <?= $o_score->academic_year_id;?></td>
    </tr>
</table>
<br>
<table class="table" width="100%">
    <tr>
        <td width="17%">Lecturer</td>
        <td width="15%">Date and Time Start</td>
        <td width="15%">Date and Time End</td>
        <td width="33%">Topics Covered</td>
        <td width="10%">Absence</td>
        <td width="10%">Note</td>
    </tr>
<?php
if ($mba_uosd) {
    foreach ($mba_uosd as $o_uosd) {
?>
    <tr>
        <td style="font-size: 11px;"><?= $o_uosd->lecturer;?></td>
        <td style="font-size: 11px;"><?= date('d M Y H:i', strtotime($o_uosd->subject_delivered_time_start));?></td>
        <td style="font-size: 11px;"><?= date('d M Y H:i', strtotime($o_uosd->subject_delivered_time_start."+1 hour"));?></td>
        <td style="font-size: 11px;"><?= $o_uosd->subject_delivered_description;?></td>
        <td style="font-size: 11px;"><?= ($o_uosd->absence_data) ? $o_uosd->absence_data->absence_status : 'PRESENT';?></td>
        <td style="font-size: 11px;"><?= ($o_uosd->absence_data) ? $o_uosd->absence_data->absence_description : '';?></td>
    </tr>
<?php
    }
}
?>
</table>
<pagebreak/>