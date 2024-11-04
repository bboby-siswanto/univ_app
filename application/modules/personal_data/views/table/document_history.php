<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            List Supporting Document
            <div class="card-header-actions">
                <button class="card-header-action btn btn-link" id="btn_new_document">
                    <i class="fa fa-plus"></i> Document
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="document_list_table" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Document Name</th>
                            <th>Document Requirement Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_document_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_document">
                <?=modules::run('personal_data/document/form_create_document', $o_personal_data->personal_data_id);?>
            </div>
        </div>
    </div>
</div>
<div id="dialog-confirm" title="Delete record" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Are you sure?</p>
</div>
<script>
    $(function() {
        var show_datatable_document = function(filter_data) {

            if($.fn.DataTable.isDataTable('table#document_list_table')){
                document_list_table.destroy();
            }

            document_list_table = $('table#document_list_table').DataTable({
                ajax: {
                    url: '<?= base_url()?>personal_data/document/filter_supporting_document',
                    type: 'POST',
                    data: filter_data
                },
                columns: [
                    {data: 'document_name'},
                    {data: 'document_type_name'},
                    {
                        data: 'document_id',
                        orderable: false,
                        render: function ( data, type, row ) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<button name="btn_download_supporting_document" type="button" data_id="'+data+'" class="btn btn-primary btn-sm" title="Download Document" ><i class="fas fa-download"></i></button>';
                            html += '<button name="btn_delete_supporting_document" type="button" data_id="'+data+'" data_link="'+row.document_requirement_link+'" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Remove Document" ><i class="fas fa-trash"></i></button>';
                            html += '</div>';
                            return html;
                        }
                    }
                ]
            });
        };

        show_datatable_document({
            personal_data_id: '<?= $o_personal_data->personal_data_id; ?>'
        });

        $('button#btn_new_document').on('click', function(e) {
            e.preventDefault();
            
            $('div#new_document_modal').modal('show');
        });

        $('table#document_list_table tbody').on('click', 'button[name="btn_download_supporting_document"]', function(e) {
            e.preventDefault();

            var s_document_id = $(this).attr("data_id");
            var s_personal_data_id = '<?= $o_personal_data->personal_data_id; ?>';
            window.location = '<?= base_url()?>file_manager/download/' + s_document_id + '/' + s_personal_data_id
        });

        $('table#document_list_table tbody').on('click', 'button[name="btn_delete_supporting_document"]', function(e) {
            e.preventDefault();
            
            var s_document_id = $(this).attr("data_id");
            var s_document_link = $(this).attr("data_link");
            var s_personal_data_id = '<?= $o_personal_data->personal_data_id; ?>';
            
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height:170,
                modal: true,
                buttons: {
                    "Delete item": function() {
                        $.blockUI();
                        $.post('<?= base_url()?>personal_data/document/delete_personal_document', {personal_data_id: s_personal_data_id, document_id: s_document_id, document_link: s_document_link}, function(result) {
                            $.unblockUI();
                            if (result.code == 0) {
                                if($.isFunction(window.show_datatable_document)){
                                    toastr['success']('Document has been deleted', 'Success!');
                                }else{
                                    window.location.reload();
                                }
                            }else{
                                toastr['error'](result.message, 'Warning!');
                            }
                        }, 'json').fail(function(xhr, txtStatus, errThrown) {
                            $.unblockUI();
                        });
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });
    });
</script>