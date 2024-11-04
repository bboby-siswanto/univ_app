<h4>Subject of Thesis. Comments maximum 250 characters</h4>
<hr>
<form url='<?=base_url()?>thesis/submit_twe_subject' id="form_submit_twe_subject" onsubmit="return false">
<input type="hidden" name="thesis_defense_id" value="<?=$thesis_defense[0]->thesis_defense_id;?>">
<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Identification of Aims and Objectives</div>
            <div class="col-sm-8">
                <textarea name="identification_objective" id="identification_objective" class="form-control"><?=($evaluation_subject_data) ? $evaluation_subject_data->identification_objective : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Thesis reflects a solid understanding of the specific topic</div>
            <div class="col-sm-8">
                <textarea name="understanding_specific_topic" id="understanding_specific_topic" class="form-control"><?=($evaluation_subject_data) ? $evaluation_subject_data->understanding_specific_topic : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Method and Project Plan</div>
            <div class="col-sm-8">
                <textarea name="method_project_plan" id="method_project_plan" class="form-control"><?=($evaluation_subject_data) ? $evaluation_subject_data->method_project_plan : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Dificulty of Thesis (low, middle, high)</div>
            <div class="col-sm-8">
                <textarea name="thesis_dificulty" id="thesis_dificulty" class="form-control"><?=($evaluation_subject_data) ? $evaluation_subject_data->thesis_dificulty : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Have similar theses presented earlier?</div>
            <div class="col-sm-8">
                <textarea name="similar_thesis" id="similar_thesis" class="form-control"><?=($evaluation_subject_data) ? $evaluation_subject_data->similar_thesis : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Grade 0 - 10</div>
            <div class="col-sm-8">
                <input type="number" name="grade_subject" id="grade_subject" class="form-control w-50" value="<?=($evaluation_subject_data) ? $evaluation_subject_data->grade : ''?>" max="10">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-left" id="btn_twe_subject_prev">Prev Page</button>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-right" id="btn_twe_subject_submit">Next Page</button>
            </div>
        </div>
    </li>
</ul>
</form>
<script>
$(function() {
    $('#btn_twe_subject_prev').on('click', function(e) {
        e.preventDefault();

        $('#pills-evaluation-process-tab').click();
    });
    
    $('#btn_twe_subject_submit').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_submit_twe_subject');
        var data = form.serialize();
        var url = form.attr('url');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                $('#pills-evaluation-potential-tab').click();
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