<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="department_list" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Department Name</th>
                        <th>Head of Department</th>
                        <th>Department Abbreviation</th>
                        <th>#</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_set_hod">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Head of Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3><span id="name_department_set"></span></h3>
                <input type="hidden" name="set_department_id" id="set_department_id">
                <div class="form-group">
                    <label for="set_employee_id">Head of Department</label>
                    <select name="set_employee_id" id="set_employee_id" class="form-control form-select">
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_set_hod_department">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
let department_list = $('#department_list').DataTable({
    processing: true,
    ajax: {
        url: '<?=site_url('hris/get_department')?>',
        data: function(d){},
        method: 'POST'
    },
    columns: [
        {data: 'department_name'},
        {data: 'personal_data_name'},
        {data: 'department_abbreviation'},
        {data: 'department_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                    html += '<button type="button" class="btn btn-info btn-sm" id="btn_set_hod" title="Set HOD"><i class="fas fa-user"></i></button>';
                    html += '<button type="button" class="btn btn-danger btn-sm" id="btn_remove_hod" title="Remove HOD"><i class="fas fa-user-times"></i></button>';
                    html += '</div> ';
                return html;
            }
        },
    ]
});
$(function() {
    $('#set_employee_id').select2({
        minimumInputLength: 2,
        allowClear: true,
        placeholder: "Please select",
        dropdownParent: $("#modal_set_hod"),
        theme: "bootstrap",
        ajax: {
            url: '<?=base_url()?>employee/get_employee_by_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    keyword: params.term,
                    status: 'active'
                };
            },
            processResults: function(result) {
                data = result.data;
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.personal_data_name,
                            id: item.employee_id
                        }
                    })
                }
            }
        }
    });

    $('#department_list tbody').on('click', '#btn_set_hod', function(e) {
        e.preventDefault();
        var data = department_list.row($(this).parents('tr')).data();

        $('#name_department_set').html(data.department_name);
        $('#set_department_id').val(data.department_id);
        $('#modal_set_hod').modal('show');
    });
    
    $('#department_list tbody').on('click', '#btn_remove_hod', function(e) {
        e.preventDefault();
        var data = department_list.row($(this).parents('tr')).data();

        if (confirm('Are you sure to remove head of department ' + data.department_name + ' ?')) {
            $.blockUI({ baseZ: 2000 });

            $.post('<?=base_url()?>hris/remove_hod_department', {department_id: data.department_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    department_list.ajax.reload(null, false);
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            })
        }
    });

    $('#btn_set_hod_department').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        var data = {
            department_id: $('#set_department_id').val(),
            employee_id: $('#set_employee_id').val(),
        };

        $.post('<?=base_url()?>hris/set_hod_department', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                department_list.ajax.reload(null, false);
                $('#modal_set_hod').modal('hide');
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error');
        })
    })
})
</script>