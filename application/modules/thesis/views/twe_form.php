<div class="row mb-2">
    <div class="col-md-12">
        <div class="button-group float-right">
            <a href="<?=base_url()?>thesis/thesis_defense" class="btn btn-info"><i class="fas fa-list"></i> Defense lists</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Thesis Work Evaluation
        <input type="hidden" name="zero_value" id="zero_value" value="<?= ($thesis_evaluation_data) ? $thesis_evaluation_data->score_evaluation_id : '' ?>">
        <!-- <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_submit_score_twe">
                <i class="fa fa-window-close"></i> Close / Submit Update Score
            </button>
        </div> -->
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
<ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="pills-evaluation-format-tab" data-toggle="pill" href="#pills-evaluation-format" role="tab" aria-controls="pills-evaluation-format" aria-selected="false">Evaluation Format</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-evaluation-process-tab" data-toggle="pill" href="#pills-evaluation-process" role="tab" aria-controls="pills-evaluation-process" aria-selected="false">Evaluation Working Process</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-evaluation-subject-tab" data-toggle="pill" href="#pills-evaluation-subject" role="tab" aria-controls="pills-evaluation-subject" aria-selected="true">Evaluation Subject</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-evaluation-potential-tab" data-toggle="pill" href="#pills-evaluation-potential" role="tab" aria-controls="pills-evaluation-potential" aria-selected="false">Evaluation Potential User</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-evaluation-content-tab" data-toggle="pill" href="#pills-evaluation-content" role="tab" aria-controls="pills-evaluation-content" aria-selected="false">Evaluation Content</a>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-evaluation-format" role="tabpanel" aria-labelledby="pills-evaluation-format-tab">
        <?= modules::run('thesis/form_evaluation_format', $thesis_defense[0]->thesis_defense_id);?>
    </div>
    <div class="tab-pane fade" id="pills-evaluation-process" role="tabpanel" aria-labelledby="pills-evaluation-process-tab">
        <?= modules::run('thesis/form_working_process', $thesis_defense[0]->thesis_defense_id);?>
    </div>
    <div class="tab-pane fade" id="pills-evaluation-subject" role="tabpanel" aria-labelledby="pills-evaluation-subject-tab">
        <?= modules::run('thesis/form_subject', $thesis_defense[0]->thesis_defense_id);?>
    </div>
    <div class="tab-pane fade" id="pills-evaluation-potential" role="tabpanel" aria-labelledby="pills-evaluation-potential-tab">
        <?= modules::run('thesis/form_potential_user', $thesis_defense[0]->thesis_defense_id);?>
    </div>
    <div class="tab-pane fade" id="pills-evaluation-content" role="tabpanel" aria-labelledby="pills-evaluation-content-tab">
        <?= modules::run('thesis/form_content', $thesis_defense[0]->thesis_defense_id);?>
    </div>
</div>
<?php
// if (($thesis_evaluation_data) AND ($thesis_evaluation_data->score_status == 'open')) {
?>
<div class="row mt-2 mb-3">
    <div class="col-6">
        <button type="button" class="btn btn-block btn-primary" id="btn_view_twe_score">
            <i class="fa fa-table"></i> View Score Result
        </button>
    </div>
    <div class="col-6">
        <!-- <button type="button" class="btn btn-block btn-success" id="btn_submit_twe_score">
            <i class="fa fa-window-close"></i> Close / Submit Update Score
        </button> -->
    </div>
</div>
<div class="row mt-2 mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-border" id="table_grading_score">
                    <thead>
                        <tr>
                            <th colspan="3">Grading Scale :</th>
                        </tr>
                        <tr>
                            <th>Score</th>
                            <th>Grade Point</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>86 - 100</td>
                            <td>4.00</td>
                            <td>A: Excelent</td>
                        </tr>
                        <tr>
                            <td>71 - 85</td>
                            <td>3.00 - 3.90</td>
                            <td>B: Good</td>
                        </tr>
                        <tr>
                            <td>56 - 70</td>
                            <td>2.00 - 2.90</td>
                            <td>C: Satisfactory</td>
                        </tr>
                        <tr>
                            <td>46 - 55</td>
                            <td>1.00 - 1.90</td>
                            <td>D: Poor</td>
                        </tr>
                        <tr>
                            <td><= 45</td>
                            <td>0.00</td>
                            <td>F: Fail</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_twe_review_score">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thesis Work Evaluation Score Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="div_twe_review_score">
                <p>Result score is here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
// }
if (($thesis_evaluation_data) AND ($thesis_evaluation_data->score_status == 'closed')) {
?>
<script>
$('input, textarea, select, button').attr('disabled', 'disabled');
$('button#btn_view_twe_score').removeAttr('disabled');
</script>
<?php
}
?>
<script>
$(function() {
    $('button#btn_view_twe_score').on('click', function(e) {
        e.preventDefault();
        var thesis_defense_id = ''

        $.post('<?=base_url()?>thesis/form_twe_review_score/<?=($thesis_defense) ? $thesis_defense[0]->thesis_defense_id : '';?>', function(result) {
            $('#div_twe_review_score').html(result.html);
            $('#modal_twe_review_score').modal('show');
        }, 'json').fail(function(params) {
            toastr.error('Error retrieve data!');
        });
    });
    $('button#btn_submit_twe_score').on('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure to submiting this score ?')) {
            $.blockUI();
            $.post('<?=base_url()?>thesis/close_twe', {evaluation_id: '<?=($thesis_evaluation_data) ? $thesis_evaluation_data->score_evaluation_id : 'x';?>'}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success');
                    setInterval(function () {location.reload();}, 2000);
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Error processing your data!', 'Error');
            });
        }
    });
});
</script>