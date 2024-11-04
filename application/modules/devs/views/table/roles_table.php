<div class="table-responsive">
    <table id="roles_table" class="table table-striped table-hover">
        <thead class="bg-dark">
            <tr>
                <th width="50px">No</th>
                <th>Roles Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script>
    $(function() {
        var roles_table = $('table#roles_table').DataTable({
            processing: true,
            ajax: {
                url: '<?=base_url()?>devs/roles/get_roles',
                type: 'POST'
            },
            columns:[
                { data: 'roles_id', orderable: false },
                { data: 'roles_name' },
                {
                    data: 'roles_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<button type="button" id="edit_roles" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button>';
                        html += '<button type="button" id="remove_roles" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                        html += '<a href="<?=base_url()?>devs/roles/pages_list/' + data + '" target="blank" class="btn btn-sm btn-info" title="List Pages"><i class="fas fa-sitemap"></i></a>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        roles_table.on( 'order.dt search.dt', function () {
            roles_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        $('button#save_roles').on('click', function(e) {
            e.preventDefault();
            $.blockUI({
                theme: true,
                baseZ: 2000
            })
            let data = $('form#form_input_roles').serialize();

            $.post('<?=base_url()?>devs/roles/save_data', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    $('#roles_input').modal('hide');
                    roles_table.ajax.reload();
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data', 'Error');
            });
        });

        $('table#roles_table tbody').on('click', 'button#edit_roles', function(e) {
            e.preventDefault();

            var roles_data = roles_table.row($(this).parents('tr')).data();
            $('#roles_id').val(roles_data.roles_id);
            $('#roles_name').val(roles_data.roles_name);
            
            $('div#roles_input').modal('show');
        });

        $('table#roles_table tbody').on('click', 'button#remove_roles', function(e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $.blockUI();
                var roles_data = roles_table.row($(this).parents('tr')).data();
                $.post('<?=base_url()?>devs/roles/remove_roles',  {roles_id : roles_data.roles_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        roles_table.ajax.reload();
                        toastr.success('Data removing!', 'Success');
                    }else{
                        toastr.warning(result.message, 'Warning');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error  processing data!', 'Error');
                });
            }
        });
    });
</script>