<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            List Academic History
            <div class="card-header-actions">
                <button class="card-header-action btn btn-link" id="btn_new_academic_history">
                    <i class="fa fa-plus"></i> Academic History
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="academic_history_list_table" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Academic Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Graduation Year</th>
                            <th>Major/Discipline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_academic_history_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new academic history</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_academic_history">
                <?=modules::run('personal_data/academic/form_create_academic_history', $o_personal_data->personal_data_id);?>
            </div>
        </div>
    </div>
</div>
<div id="dialog-confirm" title="Delete record" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Are you sure?</p>
</div>
<script>
    $(function() {
        var show_datatable_academic_history = function(filter_data) {

            if($.fn.DataTable.isDataTable('table#academic_history_list_table')){
                academic_list_table.destroy();
            }

            academic_list_table = $('table#academic_history_list_table').DataTable({
                ajax: {
                    url: '<?= base_url()?>personal_data/academic/filter_academic_history',
                    type: 'POST',
                    data: filter_data
                },
                columns: [
                    {data: 'institution_name'},
                    {data: 'address_street'},
                    {data: 'institution_email'},
                    {data: 'academic_history_graduation_year'},
                    {data: 'academic_history_major'},
                    {data: 'academic_history_id'}
                ],
                columnDefs: [
                    {
                        render: function ( data, type, row ) {
                            return data +' '+ row.address_city+' '+row.address_province+ ' ' +row.country_name+' '+row.address_zipcode;
                        },
                        targets: 1
                    },
                    {
                        targets: -1,
                        orderable: false,
                        render: function ( data, type, row ) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<button name="btn_edit_academic_history" type="button" data_id="'+data+'" class="btn btn-info btn-sm" title="Edit Data" ><i class="fas fa-edit"></i></button>';
                            html += '<<button name="btn_delete_academic_history" type="button" data_id="'+data+'" data_main="'+row.academic_history_main+'" class="btn btn-danger btn-sm" title="Remove data" ><i class="fas fa-trash"></i></button>';
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                initComplete: function(settings, json) {
                    var btn_delete = $('#academic_history_list_table tbody tr').find($('button[data_main="yes"]'));
                    btn_delete.remove();
                }
            });
        };

        show_datatable_academic_history({
            personal_data_id: '<?= $o_personal_data->personal_data_id; ?>',
            academic_history_this_job: 'no'
        });

        $('button#btn_new_academic_history').on('click', function(e) {
            e.preventDefault();
            $('form#form_edit_academic_history').find('input[type="text"], select, textarea').val('');
            $('.title-modal').text('Add new academic history');
            $('input#personal_data_id').val('<?= $o_personal_data->personal_data_id;?>');

            $('div#new_academic_history_modal').modal('toggle');
        });

        $('table#academic_history_list_table tbody').on('click', 'button[name="btn_edit_academic_history"]', function(e) {
            e.preventDefault();
            $.blockUI();

            var s_academic_history_id = $(this).attr("data_id");
            var s_personal_data_id = '<?= $o_personal_data->personal_data_id; ?>';
            $.post('<?= base_url()?>personal_data/academic/form_edit_academic_history', {academic_history_id: s_academic_history_id, personal_data_id: s_personal_data_id}, function(result) {
                $('div#modal_input_academic_history').html(result.data);
                $('.title-modal').text('Update academic data');
                $('div#new_academic_history_modal').modal('toggle');
                $.unblockUI();
            }, 'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });
        });

        $('table#academic_history_list_table tbody').on('click', 'button[name="btn_delete_academic_history"]', function(e) {
            e.preventDefault();
            
            var s_academic_history_id = $(this).attr("data_id");
            
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height:170,
                modal: true,
                buttons: {
                    "Delete item": function() {
                        $.blockUI();
                        $.post('<?= base_url()?>personal_data/academic/delete_academic_history', {academic_history_id: s_academic_history_id}, function(result) {
                            if (result.code == 0) {
                                $.unblockUI();
                                toastr['success']('Academic history has been deleted', 'Success!');
                                if($.isFunction(window.show_datatable_academic_history)){
                                    $('#form_edit_academic_history').reset();
                                }else{
                                    window.location.reload();
                                }
                            }else{
                                $.unblockUI();
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