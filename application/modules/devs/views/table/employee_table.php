<div class="table-responsive">
    <table id="employee_table" class="table table-striped table-hover">
        <thead class="bg-dark">
            <tr>
                <th width="50px">No</th>
                <th>Name</th>
                <!-- <th>Personal Email</th> -->
                <th>Employee Email</th>
                <th>ID Number</th>
                <th>Gender</th>
                <th>NIDN</th>
                <th>Status</th>
                <!-- <th>Date of Birth</th> -->
                <th>Employee Key</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script>
    $(function() {
        var employee_table = $('table#employee_table').DataTable({
            processing: true,
            order: [[ 1, "asc" ]],
            ajax: {
                url: '<?=base_url()?>devs/devs_employee/employee_list',
                type: 'POST'
            },
            columns:[
                { data: 'employee_id', orderable: false },
                { data: 'personal_data_name' },
                // { data: 'personal_data_email' },
                { data: 'employee_email' },
                { data: 'employee_id_number' },
                { data: 'personal_data_gender' },
                { data: 'employee_lecturer_number' },
                { data: 'status' },
                // { data: 'personal_data_date_of_birth' },
                { data: 'employee_id' },
                {
                    data: 'personal_data_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<button type="button" id="edit_employee" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button>';
                        // html += '<button type="button" id="remove_employee" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                        if ('<?=$this->session->userdata("employee_id")?>' == '4e2b8186-8e7b-4726-a1f5-e280d4ac0825') {
                            html += '<a href="<?=base_url()?>devs/devs_employee/change_session/' + rows.employee_id + '/employee" class="btn btn-sm btn-warning"><i class="fas fa-route"></i></a>';
                        }
                        html += '<a href="<?=base_url()?>devs/devs_employee/permission/' + rows.employee_id + '" target="blank" class="btn btn-sm btn-info" title="Permission Access"><i class="fas fa-sitemap"></i></a>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        employee_table.on( 'order.dt search.dt', function () {
            employee_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        $('button#save_employee').on('click', function(e) {
            e.preventDefault();
            $.blockUI({
                theme: true,
                baseZ: 2000
            })
            let data = $('form#form_input_employee').serialize();

            $.post('<?=base_url()?>devs/devs_employee/save_employee', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    $('#employee_input').modal('hide');
                    employee_table.ajax.reload();
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data', 'Error');
            });
        });

        $('table#employee_table tbody').on('click', 'button#edit_employee', function(e) {
            e.preventDefault();

            var employee_data = employee_table.row($(this).parents('tr')).data();
            var input = $('form#form_input_employee').find('input, select');
            if (employee_data.employee_is_lecturer == 'YES') {
                $('#employee_lecturer_number_type').removeAttr('disabled');
                $('#employee_lecturer_number').removeAttr('disabled');
            }else{
                $('#employee_lecturer_number_type').attr('disabled', 'true');
                $('#employee_lecturer_number').attr('disabled', 'true');
            }
            $.each(input, function(i, v) {
                var field = v.name;
                v.value = employee_data[field];
            });
            
            $('div#employee_input').modal('show');
        });

        $('table#employee_table tbody').on('click', 'button#remove_employee', function(e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $.blockUI();
                var employee_data = employee_table.row($(this).parents('tr')).data();
                $.post('<?=base_url()?>devs/devs_employee/remove_employee',  {employee_id : employee_data.employee_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        employee_table.ajax.reload();
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