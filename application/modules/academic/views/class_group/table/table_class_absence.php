<div class="table-responsive">
    <table id="class_absence_table" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>No.</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Study Program</th>
                <th>Batch</th>
                <th>Absence</th>
                <th>Quiz <?= ($quiz_number) ? $quiz_number : '';?></th>
                <th>Note</th>
                <th>Attachment</th>
            </tr>
        </thead>
    </table>
</div>
<!-- <div id="modal_input_score" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_add_score">Add</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->
<script>
    var quiz_number = '';
    $(function() {
        var s_subject_delivered_id = '<?= $subject_delivered_id;?>';
        let quiz_number = '<?= $quiz_number ?>';
        
        var table_class_absence = $('table#class_absence_table').DataTable({
            ajax: {
                url: '<?= base_url()?>academic/class_group/filter_class_group_member',
                type: 'POST',
                data: {class_master_id: '<?= $class_master_id?>', student_status: 'active'}
            },
            order: [
                [2, 'asc']
            ],
            paging: false,
            dom: '',
            columns: [
                {data: ''},
                {data: 'student_number'},
                {data: 'personal_data_name'},
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
                {data: 'batch'},
                {data: 'score_id'},
                {data: 'score_id'},
                {data: 'score_id'},
                {
                    data: 'score_id',
                    orderable: false,
                    render: function(data, type, row) {
                        if ('<?=$this->session->userdata("user");?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                            return '<input type="file" name="student_attachment" id="student_attachment" class="form-control attachment_' + data + '">';
                        }
                        else {
                            return '';
                        }
                    }
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function(data, type, row) {
                        return 'a';
                    }
                },
                {
                    targets: -4,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '<select name="student_absence" id="student_absence" class="form-control select_' + row.score_id + '">';
                        html += '<option value="">Please select..</option>';
                        html += '<?= $s_absence_option?>';
                        html += '</select>';
                        return html;
                    }
                },
                {
                    targets: -2,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '<input type="text" name="student_note" id="student_note" class="form-control notes_' + row.score_id + '">';
                        return html;
                    }
                },
                {
                    targets: -3,
                    orderable: false,
                    visible: (quiz_number) ? true : false,
                    render: function ( data, type, row ) {
                        var html = '<input type="text" class="b_show input-size form-control input_quiz_' + row.score_id + '" name="score_quiz" id="score_quiz">';
                        return html;
                    }
                },
                {
                    targets: [0, 1, 2, 3, 4],
                    orderable: false
                }
            ],
            createdRow: function( row, data, dataIndex){
                if (data.student_status == 'resign') {
                    $(row).addClass('bg-dark');
                }
            },
            initComplete: function(settings, json) {
                $.post('<?=base_url()?>academic/class_group/get_absence_student_lists', {subject_delivered_id : '<?= $subject_delivered_id;?>'}, function(result) {
                    var absence_student_lists = result.data;
                    if (absence_student_lists != 'false') {
                        $.each(absence_student_lists, function(idx, value) {
                            let score_id = value.score_id;
                            var desc = value.absence_description;
                            let input = $('table#class_absence_table').parent().find('.select_' + score_id);

                            if (desc != null) {
                                var absence = $.parseJSON(desc);

                                if (input.length > 0) {
                                    $('.input_quiz_' + score_id).val(absence.score_quiz);
                                }
                            }
                            if (input.length > 0) {
                                $('.select_' + score_id).val(value.absence_status);
                                $('.notes_' + score_id).val(value.absence_note);
                                if (value.absence_status == 'ABSENT') {
                                    input.closest('tr').addClass('bg-danger');
                                }
                                else if (value.absence_status == 'SICK') {
                                    input.closest('tr').addClass('bg-warning');
                                }
                                else if (value.absence_status == 'EXCUSE') {
                                    input.closest('tr').addClass('bg-info');
                                }
                            }
                        });
                    }
                }, 'json').fail(function (params) {});
            }
        });

        table_class_absence.on( 'order.dt search.dt', function () {
            table_class_absence.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        // $('table#class_absence_table tbody').on('click', 'button[name="btn_absence_input_score_quiz"]', function(e) {
        //     e.preventDefault();
        //     var score_id = table_class_absence.row($(this).parents('tr')).data();
        //     $('input#score_id_idx').val(score_id.score_id);

        //     $('input#type_score').val('quiz');
        //     $('span#score_name').text('Quiz');
        //     var score = $('table#class_absence_table tbody').find('#quiz_' + score_id.score_id).val();
        //     score = (score == 'null') ? '' : score;
        //     $('input#score_input').val(score);
        //     $('div#modal_input_score').modal('show');
        // });

        $('table#class_absence_table tbody').on('change', 'select[name="student_absence"]', function(e) {
            e.preventDefault();
            
            let absence = $(this).val();
            if (absence == 'ABSENT') {
                $(this).closest('tr').addClass('bg-danger');
            }else{
                $(this).closest('tr').removeClass('bg-danger');
            }
        });

        $('button#btn_add_quiz').on('click', function(e) {
            e.preventDefault();

            if ($('input#with_quiz').val() == '0') {
                $.post('<?= base_url()?>academic/class_group/get_class_quiz', {class_master_id: '<?= $class_master_id?>'}, function(result) {
                    if (result.code == 0) {
                        $( table_class_absence.column( -2 ).header() ).text( 'Quiz ' + result.message );
                        quiz_number = result.message;
                        table_class_absence.column( -2 ).visible( true );
                        $('input#with_quiz').val('1');
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(error) {
                    toastr.warning('System error!', 'Error!');
                });
            }else{
                table_class_absence.column( -2 ).visible( false );
                $('input#with_quiz').val('0');
            }
        });

        $('button#save_class_absence_develop').on('click', function(e) {
            e.preventDefault();

            toastr.warning("This button doesn't work yet");
            $.blockUI();

            var data_absence = [];
            table_class_absence.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                var score_id = data.score_id;
                data_absence.push({
                    score_id: score_id,
                    score_absence: $('.select_' + score_id).val(),
                    score_note: $('.notes_' + score_id).val(),
                    score_quiz: $('.input_quiz_' + score_id).val()
                });
            });

            var uosd_data = {
                employee_id: $('input#employee_id').val(),
                subject_delivered_id: $('input#subject_delivered_id').val(),
                personal_data_name: $('input#personal_data_name').val(),
                class_master_id: $('input#class_master_id').val(),
                unit_date: $('#unit_date').val(),
                unit_time: $('#unit_time').val(),
                unit_time_end: $('#unit_time_end').val(),
                unit_description: $('#unit_description').val(),
                quiz_number: quiz_number,
                uosd_absence: data_absence,
                with_quiz: $('input#with_quiz').val()
            }

            $.post('<?= base_url()?>academic/class_group/save_absence_dev', uosd_data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Absence saved', 'Success');
                    // location.href = '<?=base_url()?>academic/class_group/class_group_lists/<?= $class_master_id?>';
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error system', 'Error!');
            });
        });

        $('button#save_class_absence').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            var data_absence = [];
            table_class_absence.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                var score_id = data.score_id;
                data_absence.push({
                    score_id: score_id,
                    score_absence: $('.select_' + score_id).val(),
                    score_note: $('.notes_' + score_id).val(),
                    score_quiz: $('.input_quiz_' + score_id).val()
                });
            });

            var uosd_data = {
                employee_id: $('input#employee_id').val(),
                subject_delivered_id: $('input#subject_delivered_id').val(),
                personal_data_name: $('input#personal_data_name').val(),
                class_master_id: $('input#class_master_id').val(),
                unit_date: $('#unit_date').val(),
                unit_time: $('#unit_time').val(),
                unit_time_end: $('#unit_time_end').val(),
                unit_description: $('#unit_description').val(),
                quiz_number: quiz_number,
                uosd_absence: data_absence,
                with_quiz: $('input#with_quiz').val()
            }

            $.post('<?= base_url()?>academic/class_group/save_absence', uosd_data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Absence saved', 'Success');
                    // location.href = '<?=base_url()?>academic/class_group/class_group_lists/<?= $class_master_id?>';
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error system', 'Error!');
            });
        });
    });
</script>