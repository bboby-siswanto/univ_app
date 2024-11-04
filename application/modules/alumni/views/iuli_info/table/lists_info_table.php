<div class="card">
    <div class="card-header">
        IULI Info
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_info">
                <i class="fa fa-plus"></i> Info
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_lists_myinfo" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Add by</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_input_info_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_academic_history">
                <?=modules::run('alumni/iuli_info/form_input_info');?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#btn_new_info').on('click', function(e){
            e.preventDefault();

            var form = $('#form_input_info').find('input, select, textarea').val('');
            $('#publish').prop('checked', true);
            $('#new_input_info_modal').modal('show');
        });

        info_lists_table = $('table#table_lists_myinfo').DataTable({
            ajax: {
                url: '<?= base_url()?>alumni/iuli_info/get_data_filtered',
                type: 'POST'
            },
            columns: [
                {data: 'info_title'},
                {data: 'info_message'},
                {data: 'personal_data_name'},
                {data: 'info_status'},
                {data: 'timestamp'},
                {data: 'info_id'}
            ],
            columnDefs: [
                {
                    render: function ( data, type, row ) {
                        var res = data.toUpperCase();
                        return res;
                    },
                    targets: 3
                },
                {
                    targets: -1,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<button name="edit_info" class="btn btn-sm btn-info" title="Edit Info"><i class="fas fa-edit"></i></button>';
                        html += '<button name="draft_info" class="btn btn-sm btn-warning" title="Set Info to Draft" data_id="' + data + '" data_status="' + row.info_status + '"><i class="fas fa-folder-minus"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
            initComplete: function(settings, json) {
                table_init_complete();
            }
        });

        $('table#table_lists_myinfo tbody').on('click', 'button[name="edit_info"]', function(e) {
            e.preventDefault();
            
            var info_data = info_lists_table.row($(this).parents('tr')).data();
            var form = $('#form_input_info').find('input, select, textarea');

            form.each(function(idx, val) {
                var key_name = val.name;
                $(this).val(info_data[key_name]);
            });
            if (info_data['info_status'] == 'publish') {
                $('#publish').prop('checked', true);
            }else{
                $('#publish').prop('checked', false);
            }
            $('.title-modal').text('Edit info data');
            $('div#new_input_info_modal').modal('show');
        });

        $('table#table_lists_myinfo tbody').on('click', 'button[name="draft_info"]', function(e) {
            e.preventDefault();

            if (confirm('Are you sure?')){
                $.blockUI();
                var s_id = $(this).attr("data_id");
                var s_status = $(this).attr("data_status");

                $.post('<?= base_url()?>alumni/iuli_info/prop_status', {info_id: s_id, status: s_status}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('data has been change', 'Success!');
                        info_lists_table.ajax.reload(table_init_complete);
                    }else{
                        toastr['error'](result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                });
            }
        });
    });

    function table_init_complete() {
        var table = $('#table_lists_myinfo tbody tr');
        $.each(table, function(params) {
            var btn_toggle = $(this).find($('button#btn_set_draft'));
            var status = btn_toggle.attr('data_status');
            if (status != 'publish') {
                btn_toggle.toggleClass('btn-danger btn-success');
                btn_toggle.prop('title', 'Publish info');
            }
        });
    }
</script>