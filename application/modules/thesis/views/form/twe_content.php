<h4>Content (Academic Value). Comments maximum 250 characters</h4>
<hr>
<form url='<?=base_url()?>thesis/submit_twe_content' id="form_submit_twe_content" onsubmit="return false">
<input type="hidden" name="thesis_defense_id" value="<?=$thesis_defense[0]->thesis_defense_id;?>">
<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Problem Statement/Introdution</div>
            <div class="col-sm-8">
                <textarea name="problem_statement" id="problem_statement" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->problem_statement : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Objectives/Research questions</div>
            <div class="col-sm-8">
                <textarea name="research_question" id="research_question" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->research_question : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Theoritical/Analitycal framework</div>
            <div class="col-sm-8">
                <textarea name="analytical_framework" id="analytical_framework" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->analytical_framework : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Methods</div>
            <div class="col-sm-8">
                <textarea name="methods" id="methods" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->methods : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Results</div>
            <div class="col-sm-8">
                <textarea name="result" id="result" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->result : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Discussions</div>
            <div class="col-sm-8">
                <textarea name="discussion" id="discussion" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->discussion : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Conclusion</div>
            <div class="col-sm-8">
                <textarea name="conclusion" id="conclusion" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->conclusion : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Literature</div>
            <div class="col-sm-8">
                <textarea name="literature" id="literature" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->literature : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Have existing infrastructure in IULI been used?</div>
            <div class="col-sm-8">
                <textarea name="iuli_infrastructure" id="iuli_infrastructure" class="form-control"><?=($evaluation_content_data) ? $evaluation_content_data->iuli_infrastructure : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Grade 0 - 30</div>
            <div class="col-sm-8">
                <input type="number" name="grade_content" id="grade_content" class="form-control w-50" value="<?=($evaluation_content_data) ? $evaluation_content_data->grade : ''?>" max="30">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-left" id="btn_twe_content_prev">Prev Page</button>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-right" id="btn_twe_content_submit">Save Evaluation</button>
            </div>
        </div>
    </li>
</ul>
</form>
<script>
$(function() {
    $('#btn_twe_content_prev').on('click', function(e) {
        e.preventDefault();

        $('#pills-evaluation-potential-tab').click();
    });
    
    $('#btn_twe_content_submit').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_submit_twe_content');
        var data = form.serialize();
        var url = form.attr('url');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                setInterval(function () {
                    window.location.href = '<?=base_url()?>thesis/thesis_defense'
                }, 2000);
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