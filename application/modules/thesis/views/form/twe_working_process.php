<h4>Working Process (based on the thesis log). Comments maximum 250 characters</h4>
<hr>
<form url='<?=base_url()?>thesis/submit_twe_working_process' id="form_submit_twe_working_process" onsubmit="return false">
<input type="hidden" name="thesis_defense_id" value="<?=$thesis_defense[0]->thesis_defense_id;?>">
<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Identification of difficulty/problems</div>
            <div class="col-sm-8">
                <textarea name="identification_problem" id="identification_problem" class="form-control"><?=($evaluation_working_process_data) ? $evaluation_working_process_data->identification_problem : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Independence</div>
            <div class="col-sm-8">
                <textarea name="independence" id="independence" class="form-control"><?=($evaluation_working_process_data) ? $evaluation_working_process_data->independence : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Progress</div>
            <div class="col-sm-8">
                <textarea name="progress" id="progress" class="form-control"><?=($evaluation_working_process_data) ? $evaluation_working_process_data->progress : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Grade 0 - 10</div>
            <div class="col-sm-8">
                <input type="number" name="grade_working_process" id="grade_working_process" class="form-control w-50" value="<?=($evaluation_working_process_data) ? $evaluation_working_process_data->grade : ''?>"  max="10">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-left" id="btn_twe_working_prev">Prev Page</button>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-right" id="btn_twe_working_submit">Next Page</button>
            </div>
        </div>
    </li>
</ul>
</form>
<script>
$(function() {
    $('#btn_twe_working_prev').on('click', function(e) {
        e.preventDefault();

        $('#pills-evaluation-format-tab').click();
    });

    $('#btn_twe_working_submit').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_submit_twe_working_process');
        var data = form.serialize();
        var url = form.attr('url');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                $('#pills-evaluation-subject-tab').click();
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing your data!');
        });
    });
});
</script>