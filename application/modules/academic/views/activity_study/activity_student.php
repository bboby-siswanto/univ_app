<div class="table-responsive">
    <table id="activity_student_table" class="table table-bordered">
        <thead class="bg-dark">
            <tr>
                <th>Student Number</th>
                <th>Student Name</th>
                <th>Study Program</th>
                <th>Type</th>
                <th>Feeder Sync</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal" tabindex="-1" role="dialog" id="activity_student_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="form_activity_student" onsubmit="return false">
                    <input type="hidden" id="student_activity_study_id" name="activity_study_id" value="<?=$activity_study->activity_study_id;?>">
                    <div class="form-group">
                        <label for="input_student_name">Student Name</label>
                        <!-- <input type="text" id="input_student_name" name="student_name" class="form-control"> -->
                        <select name="student_id" id="input_student_name" class="form-control">
                <?php
                    if ($student_list) {
                        foreach ($student_list as $o_student) {
                ?>
                            <option value="<?=$o_student->student_id;?>"><?=$o_student->student_number;?> - <?=$o_student->personal_data_name;?></option>
                <?php
                        }
                    }
                ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="input_role_type">Member Role</label>
                        <select name="role_type" id="input_role_type" class="form-control">
                            <option value="">Please Select</option>
                <?php
                    foreach ($role_list as $key => $s_role) {
                ?>
                            <option value="<?= $key ;?>"><?=$s_role;?></option>
                <?php
                    }
                ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit_activity_student">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var activity_student_table = $('table#activity_student_table').DataTable({
        dom: 'Bfrtip',
        searching: false,
        paging: false,
        bInfo: false,
        buttons: [
            {
                text: 'Add Student',
                action: function ( e, dt, node, config ) {
                    $('#input_student_name').val('').trigger('change');
                    $('#input_role_type').val('');

                    $('#activity_student_modal').modal('show');
                }
            }
        ],
        processing: true,
        ajax: {
            type: 'POST',
            url: '<?=base_url()?>academic/activity_study/get_list_activity_student',
            data: function(params) {
                var a_form_data = {
                    activity_study_id: '<?=$activity_study->activity_study_id;?>'
                }
                // var a_filter_data = objectify_form(a_form_data);
                return a_form_data;
            }
        },
        columns: [
            {
                data: "student_number"
            },
            {data: "personal_data_name"},
            {
                data: "study_program_name",
                render: function(data, type, rows) {
                    if (rows['program_id'] == '<?=$this->a_programs['NI S1'];?>') {
                        return rows['study_program_ni_name'];
                    }else{
                        return rows['study_program_name'];
                    }
                }
            },
            {
                data: "role_type",
                render:  function(data, type, row) {
                    switch (data) {
                        case '1':
                            return 'Head';
                            break;

                        case '2':
                            return 'Member';
                            break;

                        case '3':
                            return 'Personal';
                            break;
                    
                        default:
                            return '';
                            break;
                    }
                }
            },
            {
                data: "student_activity_sync",
                render: function(data, type, row) {
                    if (data == 0) {
                        return '<span class="badge badge-success">Success</span>';
                    }
                    else {
                        return '<span class="badge badge-danger">Error Code: ' + data + '</span>';
                    }
                }
            },
            {
                data: "activity_student_id",
                orderable: false,
                render: function(data, type, row) {
                    var html = '<div class="btn-group" role="group">';
                    html += '<button id="btn_delete_participant_activity" class="btn btn-danger btn-sm" type="button" title="Remove"><i class="fas fa-trash"></i></button>';
                    if (row.student_activity_sync != 0) {
                        html += '<button id="btn_sync_student_activity" class="btn btn-warning btn-sm" type="button" title="Sync"><i class="fas fa-sync"></i></button>'
                    }
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    $('select#input_student_name').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        minimumInputLength: 3,
        dropdownParent: $("#activity_student_modal"),
        // ajax: {
        //     url: '<?=base_url()?>student/get_student_by_name',
        //     type: "POST",
        //     dataType: 'json',
        //     data: function (params) {
        //         return {
        //             keyword: params.term,
        //             status: 'academic'
        //         }
        //     },
        //     processResults: function(result) {
        //         data = result.data;
        //         return {
        //             results: $.map(data, function (item) {
        //                 return {
        //                     text: item.student_number + ' - ' + item.personal_data_name,
        //                     id: item.student_id
        //                 }
        //             })
        //         }
        //     }
        // }
    });

    $('table#activity_student_table tbody').on('click', 'button#btn_delete_participant_activity', function(e) {
        e.preventDefault();
        var table_data = activity_student_table.row($(this).parents('tr')).data();
        
        if (confirm('Are you sure to remove participant activity ' + table_data.personal_data_name + ' ?')) {
            $.blockUI();
            $.post('<?=base_url()?>academic/activity_study/delete_activity_student', {activity_student_id: table_data.activity_student_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    activity_student_table.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error!');
            });
        }
    });

    $('table#activity_student_table tbody').on('click', 'button#btn_sync_student_activity', function(e) {
        e.preventDefault();
        $.blockUI();
        var table_data = activity_student_table.row($(this).parents('tr')).data();
        
        $.post('<?=base_url()?>academic/activity_study/force_sync_student_activity', {activity_student_id: table_data.activity_student_id}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success', 'Success!');
                activity_student_table.ajax.reload(null, false);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error!');
        });
    });

    $('button#btn_submit_activity_student').on('click', function(e) {
        e.preventDefault();

        $.blockUI({ baseZ: 2000 });

        var data = $('#form_activity_student').serialize();
        $.post('<?=base_url()?>academic/activity_study/save_student_activity', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!', 'Success');
                $('#activity_student_modal').modal('hide');
                activity_student_table.ajax.reload(null, false);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
        
    });
})
</script>