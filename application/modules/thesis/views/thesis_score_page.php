<div class="row mb-2">
    <div class="col-md-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <a href="<?=base_url()?>thesis/thesis_defense" class="btn btn-info"><i class="fas fa-list"></i> Back to lists</a>
<?php
// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
?>
            <button type="button" class="btn btn-success" id="btn_submit_score">
                <i class="fas fa-boards"></i> Publish Score
            </button>
<?php
}
?>
            <a href="<?=base_url()?>thesis/download_score/<?=$thesis_defense[0]->thesis_defense_id;?>" target="_blank" class="btn btn-primary"><i class="fas fa-download"></i> Download PDF</a>
        </div>
    
    </div>
</div>
<div class="card">
    <div class="card-header">
        Thesis Score Result
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <div class="row">
                    <div class="col-4 mb-2">Student Name</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->personal_data_name : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Student ID</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->student_number : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Study program</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->study_program_abbreviation : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Attendance</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->attendance : ''?></div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="row">
                    <div class="col-4 mb-2">Thesis Title</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->thesis_title : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Defense Date</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? date('d F Y', strtotime($thesis_defense[0]->thesis_defense_date)) : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Defense Time</div>
                    <div class="col-8 font-weight-bold">: 
                        <?=($thesis_defense) ? date('H:i', strtotime($thesis_defense[0]->thesis_defense_time_start)).' - '.date('H:i', strtotime($thesis_defense[0]->thesis_defense_time_end)) : ''?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Defense Attendance</h4>
        <hr>
        <ul class="list-group">
<?php
if ($student_advisor) {
    foreach ($student_advisor as $o_advisor) {
?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-4">
                        Advisor <?=$o_advisor->number;?>: <?=$o_advisor->advisor_name;?>
                    </div>
                    <div class="col-sm-8"><?=$o_advisor->attendance;?></div>
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
                        Examiner <?=$o_examiner->number;?>: <?=$o_examiner->examiner_name;?>
                    </div>
                    <div class="col-sm-8"><?=$o_examiner->attendance;?></div>
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
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Final Score</h4>
        <hr>
        <table class="table table-bordered">
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
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Work Evaluation Score Details</h4>
        <hr>
        <table class="table table-bordered">
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
<div class="tab-content mt-2 mb-4">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>Thesis Presentation Evaluation Score Details</h4>
        <hr>
        <table class="table table-bordered">
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
<script>
$(function() {
    $('button#btn_submit_score').on('click', function(e) {
        e.preventDefault();
        
        if (confirm('Thesis score is <?=$thesis_defense[0]->score_final;?> / <?=$thesis_defense[0]->score_grade;?>, confirm publish score ?')) {
            $.post('<?=base_url()?>thesis/publish_defense_score', {thesis_defense_id: '<?=$thesis_defense[0]->thesis_defense_id;?>'}, function(result) {
                if (result.code == 0) {
                    toastr.success('Success upload score!');
                    setInterval(function () {
                        window.location.href = '<?=base_url()?>thesis/thesis_defense';
                    }, 1000);
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Error processing data!');
            });
        }
    });
})
</script>