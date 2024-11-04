<?php
$disable = (($examiner_data) AND ($examiner_data->examiner_lock_evaluation == 'true')) ? 'true' : 'false';
?>
<div class="row">
    <div class="col-12">
        <a href="<?=base_url()?>academic/ofse/examiner" class="btn btn-info btn-sm float-right mb-2"><i class="fas fa-list"></i> OFSE Exam List</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h3>OFSE Evaluation Sheet</h3>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <h5><?=($ofse_data) ? $ofse_data[0]->personal_data_name : '';?></h5>
                <h5><?=($ofse_data) ? $ofse_data[0]->faculty_abbreviation : '';?> / <?=($ofse_data) ? $ofse_data[0]->study_program_name : '';?></h5>
                <h5><?=($ofse_data) ? date('l', strtotime($ofse_data[0]->exam_date)).', '.date('d F Y', strtotime($ofse_data[0]->exam_date)).' '.date('H:i', strtotime($ofse_data[0]->exam_time_start)).'-'.date('H:i', strtotime($ofse_data[0]->exam_time_end)) : '';?></h5>
                <!-- <table class="table">
                    <tbody>
                        <tr>
                            <td>Student Name</td>
                            <td>: <span>nama</span></td>
                        </tr>
                        <tr>
                            <td>Study Program</td>
                            <td>: <span>Prodi</span></td>
                        </tr>
                        <tr>
                            <td>Date Time</td>
                            <td>: <span>datetime</span></td>
                        </tr>
                    </tbody>
                </table> -->
            </div>
            <div class="col-sm-6">
                <h5><?=($subject_data) ? $subject_data[0]->subject_name : '';?></h5>
                <h5>Varian Number <?=($subject_data) ? $subject_data[0]->ofse_question_sequence : '0';?></h5>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <!-- <button type="button" id="new_row_score" class="btn btn-primary"><i class="fas fa-plus"></i> Add Score</button> -->
            </div>
            <div class="col-6">
                <button type="button" id="lock_ofse_score" class="btn btn-success float-right" data-value="<?=($ofse_score_data) ? 'ok' : 'not ok';?>">2. <i class="fas fa-user-lock"></i> Sign and Lock Score</button>
                <button type="button" id="submit_ofse_score" class="btn btn-primary float-right mr-2">1. <i class="fas fa-save"></i> Save Score</button>
            </div>
        </div>
        <form url="<?=base_url()?>academic/ofse/submit_evaluation" id="form_input_evaluation" method="post" onsubmit="return false">
            <input type="hidden" name="fq_subject_question_id" id="fq_subject_question_id" value="<?= ($subject_data) ? $subject_data[0]->subject_question_id : '' ?>">
            <input type="hidden" name="fq_score_id" id="fq_score_id" value="<?= ($ofse_data) ? $ofse_data[0]->score_id : '' ?>">
            <table id="ofse_score_input_table" class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 20px !important;">#</th>
                        <th style="width: 150px !important;">Score (max. 100)</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
<?php
$index_array = 0;
$total_score = 0;
$average_score = 0;
for ($i=1; $i <= $subject_data[0]->subject_number_question ; $i++) { 
    $total_score += (($ofse_score_data) AND (isset($ofse_score_data[$index_array]))) ? $ofse_score_data[$index_array]->score : 0;
?>
                    <tr>
                        <td>
                            <?=$i;?>
                            <input type="hidden" name="subject_number_question[]" id="subject_number_question" value="<?=$i;?>">
                        </td>
                        <td>
                            <input type="number" max="100" name="ofse_input_score[]" class="form-control input_score_eval" value="<?= (($ofse_score_data) AND (isset($ofse_score_data[$index_array]))) ? $ofse_score_data[$index_array]->score : '' ?>">
                        </td>
                        <td>
                            <textarea name="ofse_input_comment[]" class="form-control"><?= (($ofse_score_data) AND (isset($ofse_score_data[$index_array]))) ? $ofse_score_data[$index_array]->comment : '' ?></textarea>
                        </td>
                    </tr>
<?php
    $index_array++;
}

$average_score = ($total_score == 0) ? 0 : $total_score/$index_array;
?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td id="total_score"><?=$total_score;?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Average</td>
                        <td id="average_score"><?=$average_score;?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="4">to calculate the total and average score</th>
                </tr>
                <tr>
                    <th>Score</th>
                    <th>Grade</th>
                    <th>Wording</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>86 – 100</td>
                    <td>A</td>
                    <td>Excellent</td>
                    <td>Outstanding performance</td>
                </tr>
                <tr>
                    <td>71 – 85</td>
                    <td>B</td>
                    <td>Good</td>
                    <td>Performance is considerably higher than the average requirements</td>
                </tr>
                <tr>
                    <td>56 – 70</td>
                    <td>C</td>
                    <td>Satisfactory</td>
                    <td>Performance meets the average requirements</td>
                </tr>
                <tr>
                    <td>46 – 55</td>
                    <td>D</td>
                    <td>Poor</td>
                    <td>Performance is below standard</td>
                </tr>
                <tr>
                    <td>≤45</td>
                    <td>F</td>
                    <td>Fail</td>
                    <td>Performance does not meet the minimum criteria, considerable further work is required</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
var disable_input = '<?= (($examiner_data) AND ($examiner_data->examiner_lock_evaluation == 'true')) ? 'true' : 'false';?>';
if (disable_input == 'true') {
    $('input, textarea, button').attr('disabled', 'disabled');
}
var ofse_score_input_table = $('table#ofse_score_input_table').DataTable({
    paging: false,
    info: false,
    ordering: false,
    searching: false,
});

$(function() {
    $('input.input_score_eval').on('keyup', function(e) {
        e.preventDefault();
        var input = $('input.input_score_eval');
        var total_score = 0;
        $.each(input, function(i, v) {
            var score = (isNaN(v.value)) ? 0 : parseFloat(v.value);
            var score = (v.value == "") ? 0 : parseFloat(v.value);
            total_score += score;
        });

        var average = total_score / parseInt('<?=$subject_data[0]->subject_number_question?>');
        $('#total_score').text(total_score);
        $('#average_score').text(average);
    });

    $('button#new_row_score').on('click', function(e) {
        e.preventDefault();

        ofse_score_input_table.row.add([
            '#',
            '<input type="number" max="100" name="ofse_input_score[]" class="form-control">',
            '<textarea name="ofse_input_comment[]" class="form-control"></textarea>',
            '<button id="remove_row" type="button" class="btn btn-danger btn-sm" title="Remove Row"><i class="fas fa-trash"></i></button>'
        ]).draw( false );
    });

    $('button#lock_ofse_score').on('click', function(e) {
        e.preventDefault();

        var value = $('button#lock_ofse_score').attr('data-value');
        if (value == '') {
            toastr.warning('Please save score first');
        }
        else if (confirm('Submit and Lock evaluation score ?')) {
            $.blockUI();
            var form = $('form#form_input_evaluation');
            var data = form.serialize();

            $.post('<?=base_url()?>academic/ofse/lock_form_evaluation', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    // setInterval(function () {
                    //     location.reload(); 
                    // }, 2000);
                }
                else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        }
    });

    $('button#submit_ofse_score').on('click', function(e) {
        e.preventDefault();

        // if (confirm('Submit evaluation score ?')) {
            $.blockUI();
            var form = $('form#form_input_evaluation');
            var url = form.attr('url');
            var data = form.serialize();

            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    // setInterval(function () {
                    //     location.reload(); 
                    // }, 2000);
                }
                else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        // }
    });

    $('table#ofse_score_input_table tbody').on('click', 'button#remove_row', function () {
        ofse_score_input_table.row($(this).parents('tr')).remove().draw();
    });
})
</script>