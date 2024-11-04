<div class="table-responsive">
    <table id="pages_table" class="table table-striped table-hover">
        <thead class="bg-dark">
            <tr>
                <th width="50px">No</th>
                <th>Pages Name</th>
                <th>Top Bar</th>
                <th>Description</th>
                <th>Pages URI</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script>
    
    var roles_id = '<?= ($roles_id) ? $roles_id : '';?>';
    var pages_table = $('table#pages_table').DataTable({
        processing: true,
        ajax: {
            url: '<?=base_url()?>devs/pages/get_pages',
            type: 'POST',
            data: function(param) {
                if (roles_id != '') {
                    param.roles_id = roles_id
                }
            }
        },
        columns:[
            { data: 'pages_id', orderable: false },
            { data: 'pages_name' },
            { data: 'pages_top_bar' },
            { data: 'pages_description' },
            { data: 'pages_uri' },
            {
                data: 'pages_id',
                orderable: false,
                visible: (roles_id) ? false : true,
                render: function(data, type, rows) {
                    var html = '<div class="btn-group" role="group" aria-label="">';
                    html += '<button type="button" id="edit_pages" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button>';
                    html += '<button type="button" id="remove_pages" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    pages_table.on( 'order.dt search.dt', function () {
        pages_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    $(function() {
        $('button#save_pages').on('click', function(e) {
            e.preventDefault();
            $.blockUI({
                theme: true,
                baseZ: 2000
            })
            let data = $('form#form_input_pages').serialize();

            $.post('<?=base_url()?>devs/pages/save_data', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    $('#pages_input').modal('hide');
                    pages_table.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data', 'Error');
            });
        });

        $('table#pages_table tbody').on('click', 'button#edit_pages', function(e) {
            e.preventDefault();

            var pages_data = pages_table.row($(this).parents('tr')).data();
            $('#pages_id').val(pages_data.pages_id);
            $('#pages_name').val(pages_data.pages_name);
            $('#pages_top_bar').val(pages_data.pages_top_bar);
            $('#pages_description').val(pages_data.pages_description);
            $('#pages_uri').val(pages_data.pages_uri);
            
            $('div#pages_input').modal('show');
        });

        $('table#pages_table tbody').on('click', 'button#remove_pages', function(e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $.blockUI();
                var pages_data = pages_table.row($(this).parents('tr')).data();
                $.post('<?=base_url()?>devs/pages/remove_pages',  {pages_id : pages_data.pages_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        pages_table.ajax.reload();
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