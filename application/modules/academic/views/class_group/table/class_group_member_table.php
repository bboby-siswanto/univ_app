<div class="card">
    <div class="card-header">Class Member - <?= $class_data->class_group_name ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p>Lecturer: <?= $lect_lists ?></p>
            </div>
            <div class="col-md-6">
                <p>Study Program: <?= $prodi_lists ?></p>
            </div>
            <div class="col-md-6">
                <p>Count Student: <?= $count_student?></p>
            </div>
        </div>
        <div class="table-responsive">
            <table id="table_class_member" class="table table-sm table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th></th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Study Program</th>
                        <th>Absence</th>
                        <th>Q1</th>
                        <th>Q2</th>
                        <th>Q3</th>
                        <th>Q4</th>
                        <th>Q5</th>
                        <th>Q6</th>
                        <th>Quiz</th>
                        <th>Final Exam</th>
                        <th>Repetition Exam</th>
                        <th>Final Score</th>
                        <th>Grade</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
            </table>
<?php
    if ($valid_approval) {
?>
            <div class="mt-4">
                <button type="button" class="btn btn-primary btn-block" id="btn_move_class">Move Student</button>
            </div>
<?php
    }
?>
        </div>
    </div>
</div>
<div id="modal_choose_class" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select new class for <span id="student_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <input type="hidden" name="score_id" id="score_id">
                    <table id="destination_class" class="table table-hover table-bordered table-striped">
                        <thead class="bg-dark">
                            <tr>
                                <th></th>
                                <th>Class Group Name</th>
                                <th>Lecturer</th>
                                <th>Study Program</th>
                                <th>Program</th>
                                <th>Count Student</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="moving_student_class" class="btn btn-primary">Moving Class</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        var class_data = $('#table_class_member').DataTable({
            ajax: {
                url: '<?= base_url()?>academic/class_group/filter_class_group_member',
                type: 'POST',
                data: {class_master_id: '<?= $class_master_id?>'}
            },
            // order: [
            //     [1, 'asc']
            // ],
            paging: false,
            columns: [
                {data: 'score_id'},
                {
                    data: 'student_number',
                    render: function(data, type, row) {
                        return '<input type="hidden" value="' + row.score_id + '">' + data;
                    }
                },
                {data: 'personal_data_name'},
                {
                    data: 'student_email',
                    render: function(data,type,row) {
                        return data + '/' + row.personal_data_email;
                    }
                },
                {data: 'study_program_abbreviation'},
                {
                    data: 'score_absence',
                    // render: function(data, type, row) {
                    //     return 
                    // }
                },
                {data: 'score_quiz1'},
                {data: 'score_quiz2'},
                {data: 'score_quiz3'},
                {data: 'score_quiz4'},
                {data: 'score_quiz5'},
                {data: 'score_quiz6'},
                {data: 'score_quiz'},
                {data: 'score_final_exam'},
                {data: 'score_repetition_exam'},
                {data: 'score_sum'},
                {data: 'score_grade'},
                // {data: 'score_id'}
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    className: 'select-checkbox',
                    render: function(data, type, row) {
                        return '';
                    }
                },
                {
                    targets: 5,
                    render: function(data, type, row) {
                        return parseFloat(data).toFixed(2) + ' %';
                    }
                }
            ],
            createdRow: function( row, data, dataIndex){
                if (data.student_semester_status != 'active') {
                    $(row).addClass('bg-secondary');
                    var td = row.children[0];
                    $(td).removeClass('select-checkbox');
                    // console.log();
                }else if ((data.score_grade == 'F') && (data.score_final_exam != null)) {
                    $(row).addClass('bad-grade');
                }
                
                if(data.score_absence > 25){
	                $(row).addClass('bad-absence');
                }
            },
            select: {
                style:    'multi',
                selector: 'td.select-checkbox'
            },
            initComplete: function(settings, json) {
                var btn_delete = $('#table_class_member tbody tr').find($('button[data-status="resign"]'));
                btn_delete.remove();
            }
        });

        var table_destination_class = $('table#destination_class').DataTable({
            ajax: {
                url: '<?= base_url()?>academic/class_group/filter_class_destination',
                type: 'POST',
                data: {
                    academic_year_id: '<?= $class_data->running_year;?>',
                    semester_type_id: '<?= $class_data->class_semester_type_id;?>',
                    subject_name: '<?= $class_data->subject_name ?>'
                }
            },
            columns: [
                {data: 'class_group_id'},
                {data: 'class_group_name'},
                {data: 'lecturer'},
                {data: 'study_prog'},
                {data: 'program_name'},
                {data: 'student_count'}
            ],
            columnDefs: [
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    render: function(data, type, row) {
                        return '';
                    }
                }
            ],
            select: {
                style:    'single',
                selector: 'td:first-child'
            },
            initComplete: function(seettings, json) {
                var filter_data = table_destination_class.rows().indexes().filter(function(value, index) {
                    if (table_destination_class.row(value).data().class_master_id == '<?= ($class_master_id) ?>') {
                        return table_destination_class.row(value).data().class_master_id;
                    }
                    return false;
                });
                table_destination_class.rows( filter_data ).remove().draw();
            }
        });

        // $('table#table_class_member tbody').on('click', 'button[name="btn_moving_student"]', function(e) {
        //     e.preventDefault();
        //     var row_data = class_data.row($(this).parents('tr')).data();
        //     $('#student_name').text(row_data.personal_data_name);
        //     $('input#score_id').val(row_data.score_id);

        //     $('div#modal_choose_class').modal('show');
        // });

        $('button#btn_move_class').on('click', function(e) {
            e.preventDefault();
            var checked = class_data.rows( { selected: true } );
            if (checked.count() > 0) {
                var a_student_name = [];
                $.each(checked.data(), function(i, v) {
                    a_student_name.push(v.personal_data_name);
                });
                $('#student_name').text('(' + a_student_name.join(' / ') + ')');
                $('div#modal_choose_class').modal('show');
            }else{
                toastr.warning('Please select one or more student!', 'Warning!');
            }
        });

        $('button#moving_student_class').on('click', function(e) {
            e.preventDefault();
            
            let class_checked = table_destination_class.rows( { selected: true } );
            let score_checked = class_data.rows( { selected: true } );
            var a_score_id_selected = [];
            $.each(score_checked.data(), function(i, v) {
                a_score_id_selected.push(v.score_id);
            });

            if (class_checked.count() > 0) {
                let class_selected = class_checked.data()[0];
                var data = {
                    score_id: a_score_id_selected,
                    class_master_id: class_selected.class_master_id
                }
                
                if (class_checked.count() == 1) {
                    $.blockUI({baseZ: 9000});
                    $.post('<?= base_url()?>academic/class_group/save_moving_student', data, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            toastr.success('Student has been moved', 'Success');
                            $('div#modal_choose_class').modal('hide');
                            class_data.ajax.reload(null, false);
                        }else{
                            toastr.warning(result.message, 'Warning!');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                    });
                }else{
                    toastr.warning('No class selected', 'Warning!');
                }
            }else{
                toastr.warning('Please select one of the classes available in the table!', 'Warning!');
            }
        });
    });
</script>