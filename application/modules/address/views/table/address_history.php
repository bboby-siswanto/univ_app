<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            List Address Data
            <div class="card-header-actions">
                <button class="card-header-action btn btn-link" id="btn_new_address_history">
                    <i class="fa fa-plus"></i> Address Data
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="address_history_list_table" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Address Name</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th>Timestamp</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_address_history_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new address data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_address_data">
                <?=modules::run('address/form_create_address', $o_personal_data->personal_data_id);?>
            </div>
        </div>
    </div>
</div>
<div id="dialog-confirm" title="Delete record" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Are you sure?</p>
</div>
<script>
    $(function() {
        $('#btn_new_address_history').on('click', function(e) {
            e.preventDefault();
            $('.title-modal').text('Add new address data');
            $('#form_edit_address').find('input[type=text]').val('');
            $('#new_address_history_modal').modal('show');
        });

        var show_datatable_address_list = function(filter_data) {

            if($.fn.DataTable.isDataTable('table#address_history_list_table')){
                address_list_table.destroy();
            }

            address_list_table = $('table#address_history_list_table').DataTable({
                ajax: {
                    url: '<?= base_url()?>address/filter_address_history',
                    type: 'POST',
                    data: filter_data
                },
                columns: [
                    {data: 'personal_address_name'},
                    {data: 'address_street'},
                    {data: 'address_phonenumber'},
                    {data: 'timestamp'},
                    {data: 'address_id'}
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
                            html += '<button name="btn_edit_address_data" type="button" data_id="'+data+'" data_type="'+row.personal_address_type+'" data-toggle="tooltip" data-placement="bottom" class="btn btn-info btn-sm" title="Edit Address Data" ><i class="fas fa-edit"></i></button>';
                            html += '<button name="btn_delete_address_data" type="button" data_id="'+data+'" data_type="'+row.personal_address_type+'" class="btn btn-danger btn-sm" title="Remove Address Data" ><i class="fas fa-trash"></i></button>';
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                initComplete: function(settings, json) {
                    var test = $('#address_history_list_table tbody tr').find($('button[data_type="primary"]'));
                    test.remove();
                }
            });
        };

        show_datatable_address_list({
            personal_data_id: '<?= $o_personal_data->personal_data_id; ?>'
        });

        $('table#address_history_list_table tbody').on('click', 'button[name="btn_edit_address_data"]', function(e) {
            e.preventDefault();
            $.blockUI();

            var s_address_id = $(this).attr("data_id");
            var s_personal_data_id = '<?= $o_personal_data->personal_data_id; ?>';
            $.post('<?= base_url()?>address/form_create_address/', {address_id: s_address_id, personal_data_id: s_personal_data_id}, function(result) {
                $('div#modal_input_address_data').html(result.data);
                $('.title-modal').text('Update Address data');
                $('div#new_address_history_modal').modal('show');
                $.unblockUI();
            }, 'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
                console.log(xhr.responseText);
            });
        });

        $('table#address_history_list_table tbody').on('click', 'button[name="btn_delete_address_data"]', function(e) {
            e.preventDefault();

            var s_address_id = $(this).attr("data_id");
            var s_personal_data_id = '<?= $o_personal_data->personal_data_id; ?>';

            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height:170,
                modal: true,
                buttons: {
                    "Delete item": function() {
                        $.blockUI();
                        $.post('<?= base_url()?>address/delete_address_data/', {address_id: s_address_id, personal_data_id: s_personal_data_id}, function(result) {
                            $.unblockUI();
                            if (result.code == 0) {
                                toastr['success']('address data has been deleted', 'Success');
                                if($.fn.DataTable.isDataTable(address_list_table)){
                                    address_list_table.ajax.reload();
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