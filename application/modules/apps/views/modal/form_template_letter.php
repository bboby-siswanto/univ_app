<form onsubmit="return false" id="form_0">
    <input type="hidden" name="template_data" id="template_data" value="generate_key_number_letter">
    <div class="row">
        <div class="col-12">
            <button class="btn btn-secondary float-right" type="button" data-dismiss="modal">Cancel</button>
            <button class="btn btn-info float-right" type="button" name="submit_form_0" id="submit_form_0">Generate</button>
        </div>
    </div>
</form>
<script>
$(function() {
    $('button#submit_form_0').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        var form = $('form#form_0');
        var form_request = $('form#form_letter_number');
        var data = form.serialize();
        var request = form_request.serialize();
        var url = "<?=base_url()?>apps/letter_numbering/generate_number";
        var request_data = request + '&' + data;
        request_data += '&template_key=' + $('select#template_list').val();

        $.post(url, request_data, function(result) {
            $.unblockUI();
            table_list.ajax.reload(null, false);
            if (result.code == 0) {
                table_list.ajax.reload(null, false);
                $('#modal_select_template').modal('hide');
                var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                var win = window.open(loc, '_blank');
                if (win) {
                    win.focus();
                }
                else {
                    window.location.href = loc;
                }
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            table_list.ajax.reload(null, false);
            $.unblockUI();
            send_ajax_error(params.responseText);
            toastr.error('Error Processing request!', 'error');
        });
    });
})
</script>