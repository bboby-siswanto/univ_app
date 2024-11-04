<div class="card">
    <div class="card-header">
        Academic Document Lists
<?php
if ($this->session->userdata('type') == 'staff') {
?>
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_add_document" data-toggle="modal" data-target="#modal_input_file">
                <i class="fa fa-plus"></i> File
            </button>
        </div>
<?php
}
?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="academic_document" class="table table-hover">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if ($list_doc) {
                foreach ($list_doc as $doc) {
            ?>
                    <tr>
                        <td><?=$doc;?></td>
                        <td>
                            <div class="btn-group">
                            <a class="btn btn-sm btn-success" href="<?=base_url()?>student/download_academic_document/<?=$doc?>" download="<?=$doc;?>" title="Download File"><i class="fas fa-download"></i></a>
                <?php
                if ($this->session->userdata('type') == 'staff') {
                ?>
                            <button id="remove_file" class="btn btn-danger btn-sm" type="button" title="Remove File"><i class="fas fa-trash"></i></button>
                <?php
                }
                ?>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal_input_file" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="file" class="form-control" name="file_upload">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="upload_file">Upload</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var table_document = $('table#academic_document').DataTable({
            paging: false,
            bInfo: false,
            // ordering: false
        });

        $('button#upload_file').on('click', function(e) {
            e.preventDefault();
            $.blockUI({baseZ: 90000});

            var formData = new FormData();
            formData.append('file_data', $('input[name="file_upload"]')[0].files[0]);
            
            $.ajax({
				url: '<?=base_url()?>academic/document/upload_document',
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(result, status, jqXHR){
                    $.unblockUI();
					if(result.code == 0){
                        toastr.success('Success upload file', 'Success!');
                        window.location.reload();
					}else{
                        toastr['error'](result.message, 'Error!');
                    }
				},
                error : function(xhr, ajaxOptions, thrownError) {
                    $.unblockUI();
                    toastr.error('Error System!', 'Error');
                    console.log(xhr.responseText);
                }
			});
        });

        $('table#academic_document tbody').on('click', 'button#remove_file', function(e) {
            e.preventDefault();
            if (confirm('Are you sure ?')) {
                var data = table_document.row($(this).parents('tr')).data();
                var file_name = data[0];

                $.blockUI();
                $.post('<?=base_url();?>academic/document/remove_doc', {filename: file_name}, function(result) {
                    toastr.success('Success remove file', 'Success!');
                    window.location.reload();
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error System!', 'Error!');
                });
            }
        });
    })
</script>