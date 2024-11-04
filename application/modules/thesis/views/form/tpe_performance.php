<h4>Evaluation Criteria. Comments maximum 250 characters</h4>
<hr>
<form url='<?=base_url()?>thesis/submit_tpe_performance' id="form_submit_tpe_performance" onsubmit="return false">
<input type="hidden" name="thesis_defense_id" value="<?=$thesis_defense[0]->thesis_defense_id;?>">
<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Presentation Performance</div>
            <div class="col-sm-8">
                <textarea name="presentation_remarks" id="presentation_remarks" class="form-control"><?=($thesis_presentation_data) ? $thesis_presentation_data->presentation_remarks : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Presentation Performance Score (0 - 30)</div>
            <div class="col-sm-8">
                <input type="number" name="presentation_score" id="presentation_score" class="form-control w-50" value="<?=($thesis_presentation_data) ? $thesis_presentation_data->presentation_score : '0'?>">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Argumentation Performance</div>
            <div class="col-sm-8">
                <textarea name="argumentation_remarks" id="argumentation_remarks" class="form-control"><?=($thesis_presentation_data) ? $thesis_presentation_data->argumentation_remarks : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Argumentation Performance Score (0 - 70)</div>
            <div class="col-sm-8">
                <input type="number" name="argumentation_score" id="argumentation_score" class="form-control w-50" value="<?=($thesis_presentation_data) ? $thesis_presentation_data->argumentation_score : '0'?>">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-info float-right" id="btn_tpe_save">Save</button>
            </div>
        </div>
    </li>
</ul>
</form>
<script>
$(function() {
    $('#btn_tpe_save').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_submit_tpe_performance');
        var data = form.serialize();
        var url = form.attr('url');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                // setTimeout(function() {location.reload();}, 1000);
                setTimeout(function() {
                    window.location.href = '<?=base_url()?>thesis/thesis_defense'
                }, 1000);
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