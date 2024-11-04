<div class="card">
    <div class="card-header">
        Roles <?= $roles_data->roles_name;?>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Pages Permission
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_setting_pages">
                <i class="fa fa-cog"></i> Settings
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="roles_pages" class="table table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>No</th>
                        <th>Pages Name</th>
                        <th>Top Bar</th>
                        <th>Description</th>
                        <th>Pages URI</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="modal_setting_pages" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setting Pages for Role <span id="setting_pages_role_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mh-500p">
                    <table id="all_pages" class="table table-striped table-hover">
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
                <button id="save_roles_pages" type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var count_select = 0;
        set_counter();
        let roles_id = '<?= $roles_data->roles_id;?>';
        let role_pages_data = JSON.parse('<?=json_encode($role_pages_data);?>');

        var roles_pages = $('table#roles_pages').DataTable({
            processing: true,
            ajax: {
                url: '<?=base_url()?>devs/pages/get_pages',
                type: 'POST',
                data: function(param) {
                    param.roles_id = roles_id
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
                    render: function(data, type, rows) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<button type="button" id="delete_pages" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        roles_pages.on( 'order.dt search.dt', function () {
            roles_pages.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    
        var all_pages = $('table#all_pages').DataTable({
            processing: true,
            paging: false,
            ajax: {
                url: '<?=base_url()?>devs/pages/get_pages',
                type: 'POST'
            },
            columns:[
                {
                    data: 'pages_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        // var checked = '';
                        // if (role_pages_data.length > 0) {
                        //     for (var i = 0; i < role_pages_data.length; i++) {
                        //         if (rows.pages_id === role_pages_data[i]['pages_id']) {
                        //             checked = 'checked';
                        //             break;
                        //         }
                        //     }
                        // }
                        var checkbox =  '<div class="custom-control custom-checkbox">' +
                                '<input type="checkbox" class="custom-control-input" id="roles_pages_' + data + '" name="pages_id[]" value="' + data + '">' +
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
            // initComplete: function(settings, json) {
            //     count_select = role_pages_data.length;
            //     set_counter();
            // }
        });

        $('table#roles_pages').on('click', 'button#delete_pages', function(e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $.blockUI();
                let data = roles_pages.row($(this).parents('tr')).data();

                $.post('<?=base_url()?>devs/roles/remove_roles_pages', {roles_pages_id: data.roles_pages_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success remove pages!', 'Success!');
                        roles_pages.ajax.reload(null, false);
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error');
                });
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

        function set_counter() {
            $('.counter_select').text('Pages selected: ' + count_select);
        }

        $('button#btn_setting_pages').on('click', function(e) {
            e.preventDefault();
            $('#pages_all').prop('checked', false);
            $('#all_pages tbody').find('input[type="checkbox"]').prop('checked', false);
            count_select = 0;
            set_counter();
            $('div#modal_setting_pages').modal('show');
        });

        $('button#save_roles_pages').on('click', function(e) {
            e.preventDefault();

            $.blockUI({
                baseZ: 2000
            });

            var submit = false;
            var checked_data = $('table#all_pages tbody').find($("input[name='pages_id[]']:checked"));
            if (checked_data.length == 0) {
                // if (confirm('Are you sure to drop all pages?')) {
                //     submit = true;
                // }
                toastr.warning('Please select one or more pages!', 'Warning!');
            }else{
                submit = true;
            }

            if (submit) {
                var roles_id = '<?=$roles_id;?>';
                item_selected = {};
                if (checked_data.length > 0) {
                    $.each(checked_data, function(i, v) {
                        var tr = $(this).parents('tr');
                        var row = all_pages.row( tr );
                        var data = row.data();
                        item_selected[i] = data;
                    });
                }

                var param_data = {
                    roles_id: '<?=$roles_id?>',
                    data: item_selected
                };

                $.post('<?=base_url()?>devs/roles/save_roles_pages', param_data, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success processing data', 'Success!');
                        $('#modal_setting_pages').modal('hide');
                        roles_pages.ajax.reload();
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error');
                });
            }
        });
    })
</script>