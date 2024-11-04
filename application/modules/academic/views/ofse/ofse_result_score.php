<div class="row">
    <div class="col-12">
        <div class="btn-group float-right mb-2" role="group" aria-label="Basic example">
            <a href="<?=base_url()?>academic/ofse/examiner" class="btn btn-info"><i class="fas fa-list"></i> OFSE Exam List</a>
            <button type="button" class="btn btn-success" id="publish_score"><i class="fas fa-check"></i> Publish</button>
        </div>
    </div>
</div>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <div class="row">
            <div class="col-sm-6">
                <h5><?=($ofse_data) ? $ofse_data[0]->personal_data_name : '';?></h5>
                <h5><?=($ofse_data) ? $ofse_data[0]->faculty_abbreviation : '';?> / <?=($ofse_data) ? $ofse_data[0]->study_program_name : '';?></h5>
                <h5><?=($ofse_data) ? date('l', strtotime($ofse_data[0]->exam_date)).', '.date('d F Y', strtotime($ofse_data[0]->exam_date)).' '.date('H:i', strtotime($ofse_data[0]->exam_time_start)).'-'.date('H:i', strtotime($ofse_data[0]->exam_time_end)) : '';?></h5>
            </div>
            <div class="col-sm-6">
                <h4><?=$ofse_subject->subject_name;?></h4>
<?php
if ($ofse_examiner) {
    foreach ($ofse_examiner as $o_examiner) {
?>
                <h5><?=ucfirst(strtolower(str_replace('_', ' ', $o_examiner->examiner_type)));?>: <?=$o_examiner->examiner_name;?></h5>
<?php
    }
}
?>
            </div>
        </div>
    </div>
</div>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>OFSE Examination Final Score</h4>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Examiner</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
<?php
$d_total_score = 0;
$i_count = 0;
if ($ofse_examiner) {
    foreach ($ofse_examiner as $o_examiner) {
        $d_total_score += $o_examiner->examiner_total_score;
        $i_count += ($o_examiner->examiner_score) ? 1 : 0;
?>
                <tr>
                    <td>
                        <?=ucfirst(strtolower(str_replace('_', ' ', $o_examiner->examiner_type)));?>
                    </td>
                    <td><?=$o_examiner->examiner_total_score;?></td>
                </tr>
<?php
    }
}
$d_average_total_score = (($d_total_score != 0) && ($i_count != 0)) ? $d_total_score / $i_count : 0;
?>
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td><?=$d_total_score;?></td>
                </tr>
                <tr>
                    <td>Average</td>
                    <td><?=$d_average_total_score;?> / <?=$this->grades->get_grade($d_average_total_score);?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="tab-content mt-2">
    <div class="tab-pane fade show active" role="tabpanel">
        <h4>OFSE Examination Detail Score</h4>
        <hr>
<?php
if ($ofse_examiner) {
    foreach ($ofse_examiner as $o_examiner) {
        $subject_data = ($o_examiner->examiner_score) ? $o_examiner->examiner_score[0] : false;
?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="3">
                        <?=$o_examiner->examiner_name;?> - <?= ($subject_data) ? 'Varian '.$subject_data->ofse_question_sequence : '';?>
                    </th>
                </tr>
                <tr>
                    <th>No.</th>
                    <th>Score</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
<?php
        $total_score = 0;
        $number_question = ($o_examiner->examiner_score) ? $o_examiner->examiner_score[0]->subject_number_question : 0;
        if ($o_examiner->examiner_score) {
            foreach ($o_examiner->examiner_score as $o_score) {
                $total_score += $o_score->score;
?>
                <tr>
                    <td><?=$o_score->score_sequence;?></td>
                    <td><?=$o_score->score;?></td>
                    <td><?=$o_score->comment;?></td>
                </tr>
<?php
            }
        }
        $d_average_score = (($total_score != 0) && ($number_question != 0)) ? $total_score / $number_question : 0;
?>
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td><?=$total_score;?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Average</td>
                    <td><?=$d_average_score;?> / <?=$this->grades->get_grade($d_average_score);?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Signature</td>
                    <td colspan="2"><?=($o_examiner->examiner_lock_evaluation == 'true') ? $o_examiner->sign_email : '';?></td>
                </tr>
            </tfoot>
        </table>
<?php
    }
}
?>
    </div>
</div>
<script>
$(function() {
    $('button#publish_score').on('click', function(e) {
        e.preventDefault();
        if (confirm('Publish ofse score to student?')) {
            $.post('<?=base_url()?>academic/ofse/publish_score', {score_id: '<?=$score_id;?>'}, function(result) {
                if (result.code == 0) {
                    toastr.success('Success!', 'Success');
                }
                else {
                    toastr.warning(result.message);
                }
            }, 'json').fail(function(params) {
                toastr.error('Error processing data!');
            });
        }
    });
})
</script>