<?= modules::run('student/show_name', $student_id, true);?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="activity_study" class="table table-hover table-bordered">
                <thead class="bg-dark">
                    <tr>
                        <th>Academic Semester <?= ($this->session->userdata('employee_id') == '4e2b8186-8e7b-4726-a1f5-e280d4ac0825') ? " | (Student/Semester)" : "" ?></th>
                        <th>Status</th>
                        <th>Credit</th>
                        <th>GPA</th>
                        <th>Credit Cummulative</th>
                        <th>GPA Cummulative</th>
                        <th>Study Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="semester_set_instituion">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false" id="form_set_institution">
                    <input type="hidden" name="student_id" id="student_id_semester">
                    <input type="hidden" name="academic_year_id" id="academic_year_id_semester">
                    <input type="hidden" name="semester_type_id" id="semester_type_id_semester">
                    <input type="hidden" name="semester_id" id="semester_id_semester">
                    <div class="form-group">
                        <label for="institution_id_semester">Institution</label>
                        <select name="institution_id" id="institution_id_semester" class="form-control">
                            <option value=""></option>
<?php
    if ($university_list) {
        foreach ($university_list as $o_university) {
?>
                            <option value="<?=$o_university->institution_id;?>">[<?=strtoupper($o_university->institution_type);?>] <?=$o_university->institution_name;?></option>
<?php
        }
    }
?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save_institution_semester">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var table_activity = $('table#activity_study').DataTable({
            searching: false,
            info: false,
            paging: false,
            ordering: false,
            ajax: {
                url: '<?=base_url()?>academic/student_academic/get_historical_study',
                data: {
                    student_id: '<?=$student_id;?>',
                    // ofse_score: true
                },
                type: 'POST'
            },
            columns: [
                {
                    data: 'academic_year_id',
                    render: function (data, type, row) {
                        var html = row.academic_year_id + ' ' + row.semester_type_name;
                    <?php
                    if ($this->session->userdata('employee_id') == '4e2b8186-8e7b-4726-a1f5-e280d4ac0825') {
                    ?>
                        html += "| (" + row.student_semester_number + ")";
                    <?php
                    }
                    ?>
                        return html;
                    }
                },
                {
                    data: 'semester_status'
                },
                {
                    data: 'credit'
                },
                {
                    data: 'cummulative_semester_score'
                },
                {
                    data: 'credit_cummulative'
                },
                {
                    data: 'cummulative_score'
                },
                {
                    data: 'student_id',
                    render: function(data, type, row) {
                        if (row['student_semester']) {
                            var student_semester_data = row['student_semester'];
                            return '<button id="change_study_location" type="button" class="btn btn-link">' +  student_semester_data.institution_name + '</button>';
                        }else{
                            return '';
                        }
                        
                    }
                },
                {
                    data: 'student_id',
                    render: function(data, type, row) {
                        // if ('<?=$this->session->userdata('user');?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                            return '<button id="remove_student_semester" type="button" class="btn btn-danger" title="Remove Semester Student"><i class="fas fa-trash"></i></button>';
                        // }else{
                        //     return '';
                        // }
                    }
                },
            ]
        });

        $('select#institution_id_semester').select2({
            minimumInputLength: 3,
            allowClear: true,
            placeholder: 'Please select...',
            theme: "bootstrap"
        });

        $('table#activity_study tbody').on('click', 'button#change_study_location', function(e) {
            var data = table_activity.row($(this).parents('tr')).data();
            var student_semester = data.student_semester;
            if (student_semester) {
                $('#institution_id_semester').val(student_semester.institution_id);
                $('#institution_id_semester').trigger('change');
            }

            $('#student_id_semester').val(data.student_id);
            $('#academic_year_id_semester').val(data.semester_academic_year_id);
            $('#semester_type_id_semester').val(data.semester_type_id);
            $('#semester_id_semester').val(data.semester_id);
            
            $('div#semester_set_instituion').modal('show');
        });

        $('table#activity_study tbody').on('click', 'button#remove_student_semester', function(e) {
            var data = table_activity.row($(this).parents('tr')).data();
            if (confirm('Confirmation for delete student semester ' + data.academic_year_id + data.semester_type_id + '?')) {
                $.blockUI();
                $.post('<?=base_url()?>academic/student_academic/delete_student_semester', {student_id: '<?=$student_id;?>', academic_year_id: data.academic_year_id, semester_type_id: data.semester_type_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success!');
                        table_activity.ajax.reload();
                    }
                    else {
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('error processing data!', 'Error');
                });
            }
        });

        $('button#save_institution_semester').on('click', function(e) {
            e.preventDefault();
            $.blockUI({baseZ: 9999});

            var data = $('form#form_set_institution').serialize();
            $.post('<?=base_url()?>academic/student_academic/save_institution_semester', data, function(result) {
                $.unblockUI();
                if (result.code == 1) {
                    toastr.warning(result.message, 'Warning!');
                }else{
                    toastr.success('Success!', 'Success')
                    $('div#semester_set_instituion').modal('hide');
                    table_activity.ajax.reload();
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'error');
            })
        });
    });
</script>