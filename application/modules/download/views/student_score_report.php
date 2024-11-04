<style>
    table.table, table.table td, table.table th {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<div style="width: 100%; text-align: center;">
    <h4>STUDENT SCORE</h4>
</div>
<p></p>
<table border="0">
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
    <tr>
        <td>Lecturer</td>
        <td>: <?= implode(', ', $a_lecturer_name)?></td>
    </tr>
</table>
<p></p>
<table style="width: 100%" class="table">
    <tr>
        <td width="20%">Student Number</td>
        <td width="40%">Student Name</td>
        <td width="15%">Score Quiz</td>
        <td width="12%">Final Exam</td>
        <td width="12%">Repetition Exam</td>
        <td width="12%">Final Score</td>
        <td width="12%">Grade</td>
    </tr>
<?php
if ($mba_score) {
    foreach ($mba_score as $o_score) {
?>
    <tr>
        <td><?= $o_score->student_number;?></td>
        <td><?= $o_score->personal_data_name;?></td>
        <td align="center"><?= intval(round($o_score->score_quiz, 0, PHP_ROUND_HALF_UP));?></td>
        <td align="center"><?= intval(round($o_score->score_final_exam, 0, PHP_ROUND_HALF_UP));?></td>
        <td align="center"><?= $o_score->d_repeat; ?></td>
        <td align="center"><?= intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));?></td>
        <td align="center"><?= $o_score->score_grade; ?></td>
    </tr>
<?php
    }
}
?>
</table>
<div>
    <p></p>
    <div style="float: left; width: 50%;">
        Received By
        <p></p><p></p><p></p>
        <u><?= ''//$s_deans; ?>Chandra Hendrianto</u><br>
        Head of Academic Service Centre
    </div>
    <div style="">
        Prepared by
        <p></p><p></p><p></p>
        <u><?= implode(' & ', $a_lecturer_name); ?></u>
        <br>
        Lecturer
    </div>
</div>