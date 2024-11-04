<style>
    td {
        vertical-align: top;
    }
</style>
<center><h2><strong>Thesis Defense Result</strong></h2></center>
<hr>
<table>
    <tr>
        <td>Student Name</td>
        <td>:</td>
        <td><?= $thesis_defense[0]->personal_data_name;?></td>
    </tr>
    <tr>
        <td>Student ID</td>
        <td>:</td>
        <td><?= $thesis_defense[0]->student_number;?></td>
    </tr>
    <tr>
        <td>Study Program</td>
        <td>:</td>
        <td><?= $thesis_defense[0]->study_program_name;?></td>
    </tr>
    <tr>
        <td>Thesis Title</td>
        <td>:</td>
        <td><?= $thesis_defense[0]->thesis_title;?></td>
    </tr>
    <tr>
        <td>Attendance</td>
        <td>:</td>
        <td><?= $thesis_defense[0]->attendance;?></td>
    </tr>
    <tr>
        <td>Defense Date</td>
        <td>:</td>
        <td><?= date('d F Y', strtotime($thesis_defense[0]->thesis_defense_date)) ;?></td>
    </tr>
    <tr>
        <td>Defense Time</td>
        <td>:</td>
        <td><?= date('H:i', strtotime($thesis_defense[0]->thesis_defense_time_start)) ;?></td>
    </tr>
</table>
<hr>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Defense Attendance</h4>
        <ul class="list-group">
<?php
if ($student_advisor) {
    foreach ($student_advisor as $o_advisor) {
?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-4">
                        Advisor <?=$o_advisor->number;?>: <?=$o_advisor->advisor_name;?> (<?=$o_advisor->attendance;?>)
                    </div>
                </div>
            </li>
<?php
    }
}

if ($student_examiner) {
    foreach ($student_examiner as $o_examiner) {
?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-4">
                        Examiner <?=$o_examiner->number;?>: <?=$o_examiner->examiner_name;?> (<?=$o_examiner->attendance;?>)
                    </div>
                </div>
            </li>
<?php
    }
}
?>
            </li>
        </ul>
    </div>
</div>
<hr>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Final Score</h4>
        <table class="table table-bordered" border="1" style=" border-collapse: collapse;">
            <thead>
                <tr>
                    <th></th>
                    <th>Thesis Work Evaluation Score</th>
                    <th>Thesis Presentation Evaluation Score</th>
                </tr>
            </thead>
            <tbody>
<?php
if ($student_advisor) {
    foreach ($student_advisor as $o_advisor) {
?>
                <tr>
                    <td>Advisor <?=$o_advisor->number;?>: <?=$o_advisor->advisor_name;?></td>
                    <td><?=($o_advisor->thesis_score) ? $o_advisor->thesis_score->score_evaluation : '';?></td>
                    <td><?=($o_advisor->thesis_score) ? $o_advisor->thesis_score->score_presentation : '';?></td>
                </tr>
<?php
    }
}
if ($student_examiner) {
    foreach ($student_examiner as $o_examiner) {
?>
                <tr>
                    <td>Examiner <?=$o_examiner->number;?>: <?=$o_examiner->examiner_name;?></td>
                    <td><?=($o_examiner->thesis_score) ? $o_examiner->thesis_score->score_evaluation : '';?></td>
                    <td><?=($o_examiner->thesis_score) ? $o_examiner->thesis_score->score_presentation : '';?></td>
                </tr>
<?php
    }
}
?>
            </tbody>
            <tfoot>
                <tr>
                    <td>Average</td>
                    <td><?=($thesis_defense) ? $thesis_defense[0]->score_evaluation_average : ''?></td>
                    <td><?=($thesis_defense) ? $thesis_defense[0]->score_presentation_average : ''?></td>
                </tr>
                <tr>
                    <td>Final Score</td>
                    <td colspan="2"><?=($thesis_defense) ? $thesis_defense[0]->score_final : ''?></td>
                </tr>
                <tr>
                    <td>Score Grade</td>
                    <td colspan="2"><?=($thesis_defense) ? $thesis_defense[0]->score_grade : ''?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<hr>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Work Evaluation Score Details</h4>
        <table class="table table-bordered" border="1" style=" border-collapse: collapse;">
            <thead>
                <tr>
                    <th rowspan="2"></th>
                    <th class="text-center" colspan="5">Criteria</th>
                    <th class="align-middle" rowspan="2">Total Score</th>
                </tr>
                <tr>
                    <th>Format</th>
                    <th>Working Process (based on the thesis log)</th>
                    <th>Subject of Thesis</th>
                    <th>Value for Potential users</th>
                    <th>Academic Value</th>
                </tr>
            </thead>
            <tbody>
<?php
if ($student_advisor) {
    foreach ($student_advisor as $o_advisor) {
?>
                <tr>
                    <td>Advisor <?=$o_advisor->number;?>: <?=$o_advisor->advisor_name;?></td>
                    <td><?=($o_advisor->thesis_score_evaluation) ? $o_advisor->thesis_score_evaluation->score_evaluation_format : '';?></td>
                    <td><?=($o_advisor->thesis_score_evaluation) ? $o_advisor->thesis_score_evaluation->score_working_process : '';?></td>
                    <td><?=($o_advisor->thesis_score_evaluation) ? $o_advisor->thesis_score_evaluation->score_subject : '';?></td>
                    <td><?=($o_advisor->thesis_score_evaluation) ? $o_advisor->thesis_score_evaluation->score_user : '';?></td>
                    <td><?=($o_advisor->thesis_score_evaluation) ? $o_advisor->thesis_score_evaluation->score_academic : '';?></td>
                    <td><?=($o_advisor->thesis_score_evaluation) ? $o_advisor->thesis_score_evaluation->score_total : '';?></td>
                </tr>
<?php
    }
}
?>
            </tbody>
        </table>
    </div>
</div>
<hr>
<div class="tab-content mt-2 mb-4">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Presentation Evaluation Score Details</h4>
        <table class="table table-bordered" border="1" style=" border-collapse: collapse;">
            <thead>
                <tr>
                    <th rowspan="2"></th>
                    <th class="text-center" colspan="2">Criteria</th>
                    <th class="align-middle" rowspan="2">Total Score</th>
                </tr>
                <tr>
                    <th>Presentation Performance</th>
                    <th>Argumentation Performance </th>
                </tr>
            </thead>
            <tbody>
<?php
if ($student_advisor) {
    foreach ($student_advisor as $o_advisor) {
?>
                <tr>
                    <td>Advisor <?=$o_advisor->number;?>: <?=$o_advisor->advisor_name;?></td>
                    <td><?=($o_advisor->thesis_score_presentation) ? $o_advisor->thesis_score_presentation->presentation_score : '';?></td>
                    <td><?=($o_advisor->thesis_score_presentation) ? $o_advisor->thesis_score_presentation->argumentation_score : '';?></td>
                    <td><?=($o_advisor->thesis_score_presentation) ? $o_advisor->thesis_score_presentation->score_total : '';?></td>
                </tr>
<?php
    }
}
if ($student_examiner) {
    foreach ($student_examiner as $o_examiner) {
?>
                <tr>
                    <td>Examiner <?=$o_examiner->number;?>: <?=$o_examiner->examiner_name;?></td>
                    <td><?=($o_examiner->thesis_score_presentation) ? $o_examiner->thesis_score_presentation->presentation_score : '';?></td>
                    <td><?=($o_examiner->thesis_score_presentation) ? $o_examiner->thesis_score_presentation->argumentation_score : '';?></td>
                    <td><?=($o_examiner->thesis_score_presentation) ? $o_examiner->thesis_score_presentation->score_total : '';?></td>
                </tr>
<?php
    }
}
?>
            </tbody>
        </table>
    </div>
</div>