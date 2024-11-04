<form id="form_input_info" onsubmit="return false">
    <input type="hidden" name="info_id" id="info_id">
    <div class="row">
        <div class="col-sm-7 col-8">
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="form-control" name="info_title" id="info_title">
            </div>
        </div>
        <div class="col-sm-5 col-4">
            <div class="form-group">
                <label></label>
                <div class="pull-right check_publish">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="publish" name="publish" checked>
                        <label class="custom-control-label" for="publish">Publish</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Message</label>
        <textarea class="form-control" name="info_message" id="info_message" cols="30" rows="5"></textarea>
    </div>
    <div class="form-group">
        <button type="button" id="btn_save_info" class="btn btn-info float-right">Submit</button>
    </div>
</form>
<script>
    $(function() {
        $('button#btn_save_info').on('click', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });

            var data = $('#form_input_info').serialize();
            $.post('<?= base_url()?>alumni/iuli_info/save_info', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#new_input_info_modal').modal('hide');
                    // info_lists_table.ajax.reload();
                    info_lists_table.ajax.reload(table_init_complete);
                    toastr['success']('Success', 'success');
                }else{
                    toastr['warning'](result.message, 'warning');
                }
            }, 'json').fail(function(xhr, textStatus, errorThrown) {
                $.unblockUI();
                toastr['warning']('Error saving data to server!', 'warning');
                // console.log(xhr.responseText);
            });
        });
    });
</script>