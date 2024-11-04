<div class="table-responsive">
    <table id="employee_pages_table" class="table table-hove table-striped">
        <thead class="bg-dark">
            <tr>
                <th width="20px">No</th>
                <th>Roles</th>
                <th>Pages Name</th>
                <th>Pages Description</th>
                <th>URI</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div id="modal_permission" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Roles</label>
                    <select name="roles_id" id="select_roles" class="form-control">
                        <option value="">Please select...</option>
                        <option value="none" selected>None</option>
            <?php
            if ($roles_lists) {
                foreach ($roles_lists as $role) {
            ?>
                        <option value="<?=$role->roles_id?>"><?=$role->roles_name;?></option>
            <?php
                }
            }
            ?>
                    </select>
                </div>
                <div class="table-responsive mh-350p">
                    <table id="all_pages" class="table table-striped table-hover table-sm">
                        <thead class="bg-dark">
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="pages_all" name="pages_all">
                                        <label class="custom-control-label" for="pages_all"></label>
                                    </div>
                                </th>
                                <th>Pages Name</th>
                                <th>Pages Description</th>
                                <th>Pages URI</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="float-left counter_select"></span>
                <button id="save_permission" type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var count_select = 0;
        set_counter();
        var employee_pages_table = $('table#employee_pages_table').DataTable({
            ajax:{
                url: '<?=base_url()?>devs/devs_employee/employee_pages_table/<?=$employee_id?>',
                type: 'POST'
            },
            columns: [
                { 
                    data: 'employee_id',
                    orderable: false
                },
                { data: 'roles_name' },
                { data: 'pages_name' },
                { data: 'pages_description' },
                { data: 'pages_uri' },
                { 
                    data: 'roles_pages_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        var html = '<div class="btn-group" role="group">';
                        html += '<button id="remove_permission_pages" class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" data-placement="bottom" title="Remove page permission"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        employee_pages_table.on( 'order.dt search.dt', function () {
            employee_pages_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        var all_pages = $('table#all_pages').DataTable({
            processing: true,
            paging: false,
            ajax: {
                url: '<?=base_url()?>devs/pages/get_pages',
                type: 'POST',
                data: function(data) {
                    var roles_id = $('select#select_roles').val();
                    data.roles_id = roles_id;
                }
            },
            columns:[
                {
                    data: 'pages_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        var checked = '';
                        var checkbox =  '<div class="custom-control custom-checkbox">' +
                                '<input type="checkbox" class="custom-control-input" id="roles_pages_' + data + '" name="pages_id[]" value="' + data + '" ' + checked + ' >' +
                                '<label class="custom-control-label" for="roles_pages_' + data + '"></label>' +
                            '</div>';
                        return checkbox;
                    }
                },
                { data: 'pages_name' },
                { data: 'pages_description' },
                { data: 'pages_uri' }
            ],
            order: [[ 1, 'asc' ]],
        });

        $('table#employee_pages_table tbody').on('click', 'button#remove_permission_pages', function name(e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $.blockUI();
                var employee_permission_data = employee_pages_table.row($(this).parents('tr')).data();
                $.post('<?=base_url()?>devs/devs_employee/remove_employee_pages_permission', {employee_id: '<?=$employee_id?>', roles_pages_id: employee_permission_data.roles_pages_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success remove data!', 'Success!');
                        employee_pages_table.ajax.reload(null, false);
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error!');
                })
            }
        });

        $('table#all_pages tbody').on('change', 'input[type="checkbox"]', function(e) {
            let data = all_pages.row($(this).parents('tr')).data();

            if(this.checked) {
                count_select++;
            }else{
                count_select--;
            }

            var datatable = all_pages.rows().data();
            if (count_select == datatable.length) {
                $('#pages_all').prop('checked', true);
            }else{
                $('#pages_all').prop('checked', false);
            }
            set_counter();
        });

        $('#pages_all').on('change', function(e) {
            e.preventDefault();
            var datatable = all_pages.rows().data();
            if(this.checked) {
                for (let i = 0; i < datatable.length; i++) {
                    $('input[type="checkbox"]').prop('checked', true);
                }
                count_select = datatable.length;
            }else{
                for (let i = 0; i < datatable.length; i++) {
                    $('input[type="checkbox"]').prop('checked', false);
                }
                count_select = 0;
            }
            set_counter();
        });

        $('select#select_roles').on('change', function(e) {
            e.preventDefault();
            var roles_id = $('#select_roles').val();
            if (roles_id == '') {
                toastr.warning('Please select user roles!', 'Warning!');
            }
            all_pages.ajax.reload();
            count_select = 0;
            set_counter();
        });

        $('button#btn_new_permission').on('click', function(e) {
            e.preventDefault();

            $('#select_roles').val('none');
            count_select = 0;
            set_counter();
            all_pages.ajax.reload();
            $('div#modal_permission').modal('show');
        });

        function set_counter() {
            $('.counter_select').text('Pages selected: ' + count_select);
        }

        $('button#save_permission').on('click', function(e) {
            e.preventDefault();

            var checked_data = $('table#all_pages tbody').find($("input[name='pages_id[]']:checked"));
            if (checked_data.length > 0) {
                $.blockUI({
                    baseZ: 2000
                });
                var employee_id = '<?=$employee_id;?>';
                item_selected = {};
                if (checked_data.length > 0) {
                    $.each(checked_data, function(i, v) {
                        var tr = $(this).parents('tr');
                        var row = all_pages.row( tr );
                        var data = row.data();
                        item_selected[i] = data;
                    });

                    var param_data = {
                        employee_id: employee_id,
                        data: item_selected
                    };

                    $.post('<?=base_url()?>devs/devs_employee/save_pages_permission', param_data, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            toastr.success('Success processing data', 'Success!');
                            $('#modal_permission').modal('hide');
                            employee_pages_table.ajax.reload();
                        }else{
                            toastr.warning(result.message, 'Warning!');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                        toastr.error('Error processing data!', 'Error');
                    });
                }
            }else{
                toastr.warning('Please select one or more pages permission!', 'Warning!');
            }
        });
    });
</script>