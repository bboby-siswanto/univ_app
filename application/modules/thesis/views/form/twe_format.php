<h4>Format (format Aspects, Layout). Comments maximum 250 characters</h4>
<hr>
<form url='<?=base_url()?>thesis/submit_twe_format' id="form_submit_twe_format" onsubmit="return false">
<input type="hidden" name="thesis_defense_id" value="<?=$thesis_defense[0]->thesis_defense_id;?>">
<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Text Style</div>
            <div class="col-sm-8">
                <textarea name="text_style" id="text_style" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->text_style : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Summary (complete)</div>
            <div class="col-sm-8">
                <textarea name="summary" id="summary" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->summary : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Chapter Structure</div>
            <div class="col-sm-8">
                <textarea name="chapter_structur" id="chapter_structur" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->chapter_structur : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Citations</div>
            <div class="col-sm-8">
                <textarea name="citations" id="citations" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->citations : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Table and Figure</div>
            <div class="col-sm-8">
                <textarea name="table_figure" id="table_figure" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->table_figure : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Layout</div>
            <div class="col-sm-8">
                <textarea name="layout" id="layout" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->layout : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">References</div>
            <div class="col-sm-8">
                <textarea name="reference" id="reference" class="form-control"><?=($evaluation_format_data) ? $evaluation_format_data->reference : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Grade 0 - 20</div>
            <div class="col-sm-8">
                <input type="number" name="grade_format" id="grade_format" class="form-control w-50" value="<?=($evaluation_format_data) ? $evaluation_format_data->grade : ''?>" max="20">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-info float-right" id="btn_twe_format_submit">Next Page</button>
            </div>
        </div>
    </li>
</ul>
</form>
<script>
$(function() {
    $('#btn_twe_format_submit').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_submit_twe_format');
        var data = form.serialize();
        var url = form.attr('url');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                $('#pills-evaluation-process-tab').click();
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