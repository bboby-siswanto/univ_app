<div class="row mb-2">
    <div class="col-md-12">
        <div class="button-group float-right">
            <a href="<?=base_url()?>thesis/thesis_defense" class="btn btn-info"><i class="fas fa-list"></i> Defense lists</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Thesis Presentation Evaluation
        <input type="hidden" name="zero_value" id="zero_value" value="<?= ($thesis_presentation_data) ? $thesis_presentation_data->score_presentation_id : '' ?>">
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
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" role="tabpanel">
    <?= modules::run('thesis/form_presentation_performance', $thesis_defense[0]->thesis_defense_id);?>
    </div>
</div>
<?php
// if (($thesis_presentation_data) AND ($thesis_presentation_data->score_status == 'open')) {
?>
<div class="row mt-2 mb-3">
    <div class="col-6">
        <button type="button" class="btn btn-block btn-primary" id="btn_view_tpe_score">
            <i class="fa fa-table"></i> View Score Result
        </button>
    </div>
    <div class="col-6">
        <!-- <button type="button" class="btn btn-block btn-success" id="btn_submit_tpe_score">
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
<div class="modal" tabindex="-1" role="dialog" id="modal_tpe_review_score">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thesis Presentation Evaluation Score Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="div_tpe_review_score">
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
if (($thesis_presentation_data) AND ($thesis_presentation_data->score_status == 'closed')) {
?>
<script>
$('input, textarea, select, button').attr('disabled', 'disabled');
$('button#btn_view_tpe_score').removeAttr('disabled');
</script>
<?php
}
?>
<script>
$(function() {
    $('button#btn_view_tpe_score').on('click', function(e) {
        e.preventDefault();

        $.post('<?=base_url()?>thesis/form_tpe_review_score/<?=($thesis_defense) ? $thesis_defense[0]->thesis_defense_id : '';?>', function(result) {
            $('#div_tpe_review_score').html(result.html);
            $('#modal_tpe_review_score').modal('show');
        }, 'json').fail(function(params) {
            toastr.error('Error retrieve data!');
        });
    });
    $('button#btn_submit_tpe_score').on('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure to submiting this score ?')) {
            $.blockUI();
            $.post('<?=base_url()?>thesis/close_tpe', {presentation_id: '<?= ($thesis_presentation_data) ? $thesis_presentation_data->score_presentation_id : 'xx';?>'}, function(result) {
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