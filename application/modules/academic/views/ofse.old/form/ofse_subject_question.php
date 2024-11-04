<form url="<?=base_url()?>academic/ofse/submit_ofse_question" id="form_submit_ofse_question" onsubmit="return false" method="post">
    <input type="hidden" name="ofse_period_id" id="ofse_period_id" class="v_value" value="<?=$ofse_period_id;?>">
    <input type="hidden" name="ofse_subject_code" id="ofse_subject_code" class="v_value" value="<?=$ofse_subject_code;?>">
    <input type="hidden" name="ofse_subject_id" id="ofse_subject_id" class="v_value" value="<?=$subject_id;?>">
<?php
if ($count_student > 0) {
?>
    <small class="text-danger">Only .pdf format can be uploaded</small>
    <div class="row">
<?php
    $count_student = $count_student + 1;
    for ($i=1; $i <= $count_student; $i++) { 
?>
        <div class="col-sm-6 mb-1">
            <div class="border rounded p-2">
                <label>Question Number <?=$i;?></label>
                <table class="table">
                    <tr>
                        <td width="100px">Add File</td>
                        <td>
                            <input type="file" name="question_file[]" class="form-control v_value">
                            <input type="hidden" name="number[]" class="v_value" value="<?=$i;?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php
    }
?>
    </div>
<?php
?>
<hr>
<div class="row">
    <div class="col-12">
        <button id="btn_submit_file_question" class="btn btn-info float-right" type="button">Save Changes</button>
        <button id="btn_cancel_file_question" class="btn btn-secondary float-right mr-2" type="button" data-dismiss="modal">Close</button>
    </div>
</div>
<?php
}
?>
</form>
<script>
$(function() {
    $('button#btn_submit_file_question').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_submit_ofse_question');
        var form_data = new FormData(form[0]);
        // console.log(form_data);
        $.ajax({
            url: form.attr('url'),
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function (xhr, status, error) {
                toastr.error('Error processing data!');
            },
            success: function(rtn){
                if (rtn.code == 0) {
                    ofse_subject_list.ajax.reload(null, false);
                    $('#modal_subject_question').modal('hide');
                }
                else {
                    toastr.warning(rtn.message, 'Warning!');
                }
            }
        });
        // if (form.files.length > 0) {
        //     console.log('masuk');
        // }
        // else {
        //     toastr.warning('No file invluded!');
        // }
    });
})
</script>