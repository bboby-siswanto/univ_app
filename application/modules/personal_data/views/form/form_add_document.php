<form method="post" id="form_add_document" onsubmit="return false">
    <input type="hidden" name="personal_data_id" id="personal_data_id" value="<?= $personal_data_id; ?>">
    <div class="form-group">
        <label>Document Type</label>
        <select name="document_id" id="document_id" class="form-control" required>
            <option value="" selected disabled>---</option>
        <?php
            foreach ($o_requirement_document as $doc) {
        ?>
            <option value="<?= $doc->document_id; ?>" <?= (($personal_document) AND ($personal_document->document_id == $doc->document_id)) ? 'selected' : ''; ?>><?= $doc->document_name; ?></option>
        <?php
            }
        ?>
        </select>
    </div>
    <div class="form-group">
        <input type="file" name="document_file" id="document_file" require>
        <div><small class="text-danger">File should be less than 2MB</small></div>
    </div>
    <button class="btn btn-primary" id="btn_add_document">Save</button>
</form>
<script>
    $(function() {
        $('#btn_add_document').on('click', function(e) {
            e.preventDefault();
            $('div#new_document_modal').modal('hide');
            $.blockUI();
            var form = $('form#form_add_document');
			
			var formData = new FormData();
			formData.append('document_type', $('select#document_id').val());
			formData.append('file', $('input[type=file]')[0].files[0]);
			formData.append('personal_data_id', '<?=$personal_data_id?>');

            var url = '<?=base_url()?>personal_data/document/new_document';

            $.ajax({
				url: url,
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(rtn, status, jqXHR){
					if(rtn.code == 0){
                        if($.isFunction(window.show_datatable_document)){
                            toastr['success']('File has been uploaded', 'Success!');
                        }else{
                            window.location.reload();
                        }
                        $.unblockUI();
					}else{
                        $.unblockUI();
                        toastr['error'](rtn.message, 'Error!');
                    }
				},
                error : function(xhr, ajaxOptions, thrownError) {
                    $.unblockUI();
                    console.log(xhr.responseText);
                }
			});
        });
    });
</script>