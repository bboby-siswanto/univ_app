<div class="card">
    <h4><?=$student_data->personal_data_name.' ('.$student_data->study_program_abbreviation.')';?></h4>
    <hr>
    <form id="form_submit_allfile" url="<?=base_url()?>thesis/submit_all_document_thesis" onsubmit="return false">
        <input type="hidden" name="student_id" value="<?=$student_data->student_id;?>">
        <div class="row">
<?php
    if ($list_filetype) {
        foreach ($list_filetype as $s_filetype) {
?>
        
            <div class="col-md-6">
                <div class="form-group">
                    <label><?=$s_filetype;?></label>
                    <input type="file" name="<?=$s_filetype;?>" class="form-control">
                </div>
            </div>
<?php
        }
    }
?>
        </div>
    </form>
    <button type="button" id="btn_submit_files" class="btn btn-success btn-block">Submit</button>
</div>
<script>
$(function() {
    $('button#btn_submit_files').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 2000});

        var form = $('#form_submit_allfile');
        var form_data = new FormData(form[0]);
        
        $.ajax({
            url: form.attr('url'),
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function (xhr, status, error) {
                $.unblockUI();
                toastr.error('Error processing data!');
            },
            success: function(rtn){
                $.unblockUI();
                if (rtn.code == 0) {
                    toastr.success('Success', 'Sukses!');
                }
                else {
                    toastr.warning(rtn.message, 'Warning!');
                }
            }
        });
    });
})
</script>