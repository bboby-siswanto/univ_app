<table id="table_class_group" class="table table-border table-striped">
    <thead class="bg-dark">
        <tr>
            <th></th>
            <th>Class Group Name</th>
            <th>Lecturer</th>
            <th>Study Program</th>
            <th>Subject</th>
            <th>Count Student</th>
            <th>Uploaded All Score</th>
            <th class="d-none">Subject Name</th>
            <th class="d-none">SKS</th>
            <th class="d-none">Lecturer Mail</th>
            <th class="d-none">Class Semester</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<div class="modal" tabindex="-1" role="dialog" id="modal_select_lect">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Lecturer <span id="subject_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <input type="hidden" id="count_sks">
                    <table id="table_lect_modal" class="table table-bordered table-hover table-striped">
                        <thead class="bg-dark">
                            <tr>
                                <th></th>
                                <th>Lecturer Name</th>
                                <th>Credit Allocation</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_merging_class" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_link_input">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Link Exam</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="link_class_master_id" name="link_class_master_id">
                <input type="hidden" id="class_master_link_exam_available" name="class_master_link_exam_available" value="enable">
                <div class="form-group">
                    <label for="link_exam">Link Exam</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i id="setting_link" role="button" class="fas fa-eye text-success c-pointer"></i>
                            </div>
                        </div>
                        <input type="text" id="link_exam" name="link_exam" class="form-control">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_link_exam">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var lect_list = false;
    // var show_class_datatable = function() {
    //     if ($.fn.DataTable.isDataTable('table#table_class_group')) {
    //         class_group_lists_table.destroy();
    //     }
    $(document).keypress(function(e) {
        if(e.charCode == 48) {
            $('#table_class_group_filter input').focus();
        }
    });
    
    $('#link_exam').keypress(function(e) {
        if(e.charCode == 13) {
            $('#submit_link_exam').click();
        }
    });

        class_group_lists_table = $('table#table_class_group').DataTable({
            ajax: {
                url: '<?= base_url()?>academic/class_group/filter_class_group_data',
                type: 'POST',
                data: function(d){
                    d.academic_year_id = $('#academic_year_id').val(),
                    d.semester_type_id = $('#semester_type_id_search').val()
                }
            },
            processing: true,
            dom: 'Bfrtip',
            orderCellsTop: true,
            order: [
                [1, 'asc']
            ],
            buttons: [
                {
                    extend: 'collection',
                    text: 'Excel Download',
                    className: 'btn-fw-grey border-0 with-data-toggle',
                    buttons: [
                        {
                            extend: 'excel',
                            text: 'Based on Class Group',
                            exportOptions: {
                                columns: [1,2,3,4,5,6,7,8,9]
                            }
                            // exportOptions: {columns: ':visible'}
                        },
                        {
                            text: "Based on Lecturer",
                            action: function ( e, dt, node, config ) {
                                var s_academic_year = $('#academic_year_id').val();
                                var s_semester_type = $('#semester_type_id_search').val();
                                window.location.href = '<?=base_url()?>download/excel_download/download_class_by_lecturer/' + s_academic_year + '/' + s_semester_type + '/true';
                            }
                        }
                    ]
                }
            ],
            columns: [
                {data: 'class_group_id'},
                {data: 'class_group_name'},
                {data: 'lecturer'},
                {data: 'study_prog'},
                {data: 'study_subject'},
                {data: 'student_count'},
                {
                    data: 'has_upload_score',
                    render: function(data, type, row) {
                        return (data) ? 'TRUE' : 'FALSE';
                    }
                },
                {
                    data: 'study_subject',
                    visible: false
                },
                {
                    data: 'curriculum_subject_credit',
                    visible: false
                },
                {
                    data: 'lecturer_email',
                    visible: false
                },
                {
                    data: 'class_student_semester',
                    visible: false,
                    render: function(data, type, row) {
                        return '' + data;
                    }
                },
                {data: 'class_master_id'}
            ],
            initComplete: function (settings, json ) {
                table_init_complete(json);
            },
            columnDefs: [
                {
                    targets: 4,
                    render: function(data, type, row) {
                        return data + ' ( ' + row.curriculum_subject_credit + ' SKS )';
                    }
                },
                {
                    targets: 1,
                    render: function(data, type, row) {
                        var btn = data;
                        var class_master = row.class_master_id;
                        if (class_master != null) {
                            btn = '<a href="<?=base_url()?>academic/class_group/class_group_lists/' + row.class_master_id + '" target="blank">' + data + '</a>';
                        }
                        return btn;
                    }
                },
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    render: function(data, type, ui) {
                        var class_master = ui.class_master_id;
                        var html = '<input type="hidden" value="' + data + '" name="class_master_id">';
                        return html;
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '';
                        var class_master = row.class_master_id;
                        if (class_master != null) {
                            html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<a href="<?=base_url()?>academic/class_group/class_group_lists/' + row.class_master_id + '" class="btn btn-info btn-sm" id="btn_view_score" name="btn_view_score" title="view score absence" target="blank"><i class="fas fa-address-card"></i></a>';
                            html += '<?= $btn_html ?>';
                            html += '<button class="btn btn-info btn-sm" type="button" name="btn_send_score" id="btn_send_score" title="send score template"><i class="fas fa-paper-plane"></i></button>';
                            // html += '<button class="btn btn-info btn-sm" type="button" id="btn_sync_feeder" title="Sync to feeder"><i class="fas fa-exchange-alt"></i></button>';
                            html += '<a href="<?=base_url()?>feeder/sync_master_class/' + class_master +  '" target="_blank" class="btn btn-info btn-sm" title="Sync to feeder"><i class="fas fa-exchange-alt"></i></a>';
                            html += '<button type="button" name="btn_add_link" id="btn_add_link" class="btn btn-sm btn-success" title="add link exam"><i class="fas fa-upload"></i></button>';
                            html += '</div>';
                        }
                        return html;
                    }
                }
            ],
            select: {
                style:    'multi',
                selector: 'td:first-child'
            }
        });
    // }

    $(function() {
        $('table#table_class_group tbody').on('click', 'button[name="btn_send_score"]', function(e) {
            e.preventDefault();

            var class_data = class_group_lists_table.row($(this).parents('tr')).data();
            var s_class_master_id =class_data.class_master_id; 
            $.blockUI();
            $.post('<?=base_url()?>academic/class_group/send_score_template', {class_master_id: s_class_master_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Score template send!', 'Success!');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Error send score  template!<br>Please try again later or contact the  IT Team', 'Error!');
                $.unblockUI();
            })
        });
        
        $('table#table_class_group tbody').on('click', 'button#btn_add_link', function(e){
            e.preventDefault();
            
            var class_data = class_group_lists_table.row($(this).parents('tr')).data();

            $('#link_class_master_id').val(class_data.class_master_id);
            $('#link_exam').val(class_data.class_master_link_exam);
            $('#class_master_link_exam_available').val(class_data.class_master_link_exam_available);
            if (class_data.class_master_link_exam_available == 'enable') {
                $('#setting_link').removeClass('text-secondary').addClass('text-success');
            }
            else {
                $('#setting_link').removeClass('text-success').addClass('text-secondary');
            }
            
            $('#modal_link_input').modal('show');
        });

        $('#modal_link_input').on('shown.bs.modal', function() {
            $('input#link_exam').focus();
        })

        $('#setting_link').on('click', function(e) {
            e.preventDefault();

            if ($('#class_master_link_exam_available').val() == 'enable') {
                $('#class_master_link_exam_available').val('disable');
                $('#setting_link').removeClass('text-success').addClass('text-secondary');
            }
            else {
                $('#class_master_link_exam_available').val('enable');
                $('#setting_link').removeClass('text-secondary').addClass('text-success');
            }
        });

        $('table#table_class_group tbody').on('click', 'button#btn_sync_feeder', function(e){
	        e.preventDefault();
           $.blockUI();
	       
	       var class_data = class_group_lists_table.row($(this).parents('tr')).data();
	       var s_class_master_id = class_data.class_master_id;
	       
	    //    $.post('<?=base_url()?>feeder/sync_class_master/' + s_class_master_id, function(result) {
        //         $.unblockUI();
        //         toastr.success('Finish sync class!', 'Finish!');
        //         console.log(result);
        //    }, 'json').fail(function(params) {
        //         toastr.success('Finish sync class!', 'Finish!');
        //         $.unblockUI();
        //    });
        });

        var class_lecturer_modal = function(data) {
            if($.fn.DataTable.isDataTable('table#table_lect_modal')){
                lect_table_modal.destroy();
            }

            lect_table_modal = $('table#table_lect_modal').DataTable({   
                data: data,
                columns: [
                    {data: ' '},
                    {data: 'personal_data_name'},
                    {data: 'credit_allocation'}
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function(data, type, ui) {
                            return '<input type="number" class="form-control" name="new_credit_allocation[]" id="new_credit_allocation_' + ui.class_group_lecturer_id + '" value="' + data + '">';
                        }
                    },
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0,
                        render: function(data, type, ui) {
                            var html = '';
                            return html;
                        }
                    }
                ],
                select: {
                    style:    'multi',
                    selector: 'td:first-child'
                }
            });
        }

        $('button#submit_merging_class').on('click', function(e) {
            e.preventDefault();

            let sks_subject = $('#count_sks').val();

            var checked = lect_table_modal.rows( { selected: true } );
            var count_checked = checked.count();
            if (count_checked > 0) {
                var data_checked = checked.data();
                var i_sum_sks = 0;
                var a_class_lect_data = [];
                for (let i = 0; i < count_checked; i++) {
                    var sks_allocation = parseFloat($('#new_credit_allocation_'+data_checked[i].class_group_lecturer_id).val());
                    if (isNaN(sks_allocation)) {
                        sks_allocation = 0;
                    }
                    a_class_lect_data.push({class_lect_id: data_checked[i].class_group_lecturer_id, sks_allocation: sks_allocation});
                    i_sum_sks += sks_allocation;
                }

                if (parseFloat(sks_subject) != parseFloat(i_sum_sks)) {
                    toastr['warning']('Credit allocation lecturer selected must equals ' + sks_subject + '!', 'Warning');
                }else{
                    proccessing_join(a_class_lect_data);
                }
            }else{
                toastr['warning']('Please select lecturer!', 'Warning');
                return false;
            }
        });

        $('button#btn_new_class_group').on('click', function(e) {
            e.preventDefault();
            $.post('<?= base_url()?>academic/class_group/form_create_class_group', function(result) {
                $('div#modal_input_class_group').html(result.data);
                $('.title-modal').text('Add new class group');
                $('div#class_group_modal').modal('show');
            }, 'json');
        });

        $('#form_filter_class_group').on('submit', function(e) {
            e.preventDefault();
            var s_academic_year = $('#academic_year_id').val();
            var s_semester_type_id = $('#semester_type_id_search').val();

            if ((s_academic_year == '') || (s_semester_type_id == '')) {
                toastr['warning']('Please select filter field!', 'Warning');
            }else{
                class_group_lists_table.ajax.reload(function(json) {
                    table_init_complete(json);
                });
                
                // show_class_datatable();
            }
            return false;
        });

        $('button#btn_join_class_group').on('click', function(e) {
            e.preventDefault();

            if ($.fn.DataTable.isDataTable('table#table_class_group')) {
                var checked = class_group_lists_table.rows({selected: true});
                var count_checked = checked.count();
                let table_data = class_group_lists_table.rows();

                if (table_data.count() == 0) {
                    toastr.warning('No data in this table', 'Warning!');
                }else{
                    $.blockUI();
                    if (count_checked > 0) {
                        var data_checked = checked.data();
                        var a_class_id = [];
                        for (let i = 0; i < data_checked.length; i++) {
                            var class_id = data_checked[i].class_group_id;
                            a_class_id.push(class_id);
                        }

                        var a_data = {
                            data: a_class_id,
                            academic_year_id: $('#academic_year_id').val(),
                            semester_type_id : $('#semester_type_id_search').val()
                        }
                    }else{
                        var a_data = {
                            academic_year_id: $('#academic_year_id').val(),
                            semester_type_id : $('#semester_type_id_search').val()
                        }
                    }

                    $.post('<?=base_url()?>academic/class_group/start_merging_class', a_data, function(result) {
                        $.unblockUI();
                        if (result.code == 1) {
                            toastr.warning(result.message, 'Warning!');
                        }else{
                            toastr['success']('Success merging class', 'Success');
                            class_group_lists_table.$('input').removeAttr( 'checked' );
                            $('#modal_select_lect').modal('hide');
                            class_group_lists_table.ajax.reload(function(json) {
                                table_init_complete(json);
                            }, false);
                        }
                    }, 'json').fail(function(a, b, c) {
                        $.unblockUI();
                        toastr.error('Error processing data!', 'Error!');
                    });
                }
            }else{
                toastr.warning('No data in this table', 'Warning!');
            }
        })

        $('button#btn_join_class_groups').on('click', function(e) {
            e.preventDefault();
            
            var checked = class_group_lists_table.rows( { selected: true } );
            var count_checked = checked.count();

            let table_data = class_group_lists_table.rows();
            
            if (table_data.count() == 0) {
                toastr['warning']('No data in class table', 'Warning');
            }else{
                $.blockUI();
                if (count_checked > 1) {
                    var data_checked = checked.data();
                    var a_class_id = [];
                    for (let i = 0; i < data_checked.length; i++) {
                        var class_id = data_checked[i].class_group_id;
                        a_class_id.push(class_id);
                    }
                    
                    $.post('<?= base_url()?>academic/class_group/checked_mastering_data', {data: a_class_id, academic_year_id: $('#academic_year_id').val(), semester_type_id : $('#semester_type_id_search').val()}, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            let data_ = result.data;
                            if (data_.lecturer.length > 1) {
                                let lecturer_data = data_.lecturer;
                                $('#count_sks').val(data_.total_sks).attr('data-class_id', class_id);
                                $('#subject_name').text(data_.subject_name);
                                class_lecturer_modal(lecturer_data);
                                $('#modal_select_lect').modal('show');
                            }else{
                                var empl_data = data_.lecturer;
                                var a_class_lect_data = [];
                                a_class_lect_data.push({class_lect_id: empl_data[0].class_group_lecturer_id, sks_allocation: empl_data[0].credit_allocation});
                                
                                if (confirm("Class is available, continue merging class?")) {
                                    proccessing_join(a_class_lect_data);
                                }
                            }
                        }else{
                            toastr['warning'](result.message, 'Warning');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                    });
                }else{
                    $.post('<?=base_url()?>academic/class_group/commit_mastering_class', {academic_year_id: $('#academic_year_id').val(), semester_type_id : $('#semester_type_id_search').val()}, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            toastr['success']('Commit success', 'Success');
                            class_group_lists_table.$('input').removeAttr( 'checked' );
                            class_group_lists_table.ajax.reload(function(json) {
                                table_init_complete(json);
                            }, false);
                        }else{
                            toastr['warning'](result.message, 'Warning!');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                    });
                }
            }
        });

        $('#submit_link_exam').on('click', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });

            var data = {
                class_master_id: $('#link_class_master_id').val(),
                link_exam: $('#link_exam').val(),
                class_master_link_exam_available: $('#class_master_link_exam_available').val()
            };
            $.post('<?=base_url()?>academic/class_group/submit_link', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#modal_link_input').modal('hide');
                    class_group_lists_table.ajax.reload(null, false);
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
                
            }, 'json').fail(function(e) {
                toastr.error("Error processing data");
            })
        });

        function proccessing_join(a_employee_data_selected) {
            $.blockUI({ baseZ: 2000 });

            var class_checked = class_group_lists_table.rows( { selected: true } );
            var class_data_checked = class_checked.data();
            var class_id = [];
            var lect_selected = [];
            for (let i = 0; i < class_data_checked.length; i++) {
                var id_class = class_data_checked[i].class_group_id;
                class_id.push(id_class);
            }

            $.post('<?=base_url()?>academic/class_group/save_class_mastering', {data: class_id, class_lect_data : a_employee_data_selected, academic_year_id: $('#academic_year_id').val(), semester_type_id : $('#semester_type_id_search').val()}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr['success']('Success merging class', 'Success');
                    class_group_lists_table.$('input').removeAttr( 'checked' );
                    $('#modal_select_lect').modal('hide');
                    class_group_lists_table.ajax.reload(function(json) {
                        table_init_complete(json);
                    }, false);
                }else{
                    toastr['warning'](result.message, 'Warning');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        }
    });

    function table_init_complete(json) {
        // console.log(class_group_lists_table);
        if (json.data.length > 0) {
            if ($('table#table_class_group thead tr').length > 1) {
                $('table#table_class_group thead tr:eq(1)').remove();
            }
            var group_lecturer = json.group_lecturer.sort();
            var group_study_program = json.group_study_program.sort();
            console.log(group_study_program);
            lect_list = json.group_lecturer_email;
            // console.log($('table#table_class_group thead tr:eq(1) th'));

            $('table#table_class_group thead tr:eq(0)').clone(true).appendTo( 'table#table_class_group thead' );
            $('table#table_class_group thead tr:eq(1) th').each( function (i) {
                
                $(this).unbind().removeAttr('class');
                $(this).html('');
                if ((i < 6) && (i > 0)) {
                    var title = $(this).text();
                    var column = this;

                    var select = $('<select class="form-control">').appendTo($(this));
                    select.append($('<option>').attr('value', '').text('---'));
                    
                    // if (i > 3) {
                    //     i++;
                    // }

                    if (i==2) {
                        for(var a=0; a<group_lecturer.length; a++) {
                            select.append('<option value="' + group_lecturer[a] +'">' + group_lecturer[a] +'</option>');
                        }
                    }else if (i==3) {
                        for(var a=0; a<group_study_program.length; a++) {
                            select.append('<option value="' + group_study_program[a] +'">' + group_study_program[a] +'</option>');
                        }
                    }else{
                        class_group_lists_table.column(i).data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        });
                    }

                    $('select', this).on('change', function() {
                        if (class_group_lists_table.column(i).search() != this.value) {
                            class_group_lists_table
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                }
            } );
        }
        else{
            $('table#table_class_group thead tr:eq(1)').remove();
            lect_list = false;
        }
    }
</script>