<div class="card">
    <div class="card-body">
        <form onsubmit="return false" id="form_filter_defense">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Academic Year</label>
                        <select name="academic_year_defense" id="academic_year_defense" class="form-control">
                <?php
                if (isset($academic_year_list) AND (count($academic_year_list) > 0)) {
                    foreach ($academic_year_list as $o_academic_year) {
                        $selected = ($this->session->userdata('academic_year_id_active') == $o_academic_year->academic_year_id) ? 'selected=""' : '';
                ?>
                            <option value="<?=$o_academic_year->academic_year_id;?>" <?=$selected;?>><?=$o_academic_year->academic_year_id;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Semester Type</label>
                        <select name="semester_type_defense" id="semester_type_defense" class="form-control">
                <?php
                if (isset($semester_type_list) AND (count($semester_type_list) > 0)) {
                    foreach ($semester_type_list as $o_semester_type) {
                        $selected = ($this->session->userdata('semester_type_id_active') == $o_semester_type->semester_type_id) ? 'selected=""' : '';
                ?>
                            <option value="<?=$o_semester_type->semester_type_id;?>" <?=$selected;?>><?=$o_semester_type->semester_type_name;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-info" id="btn_filter_defense">Filter</button>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Student List of Thesis Defense
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="defense_list_table" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Study Program</th>
                        <th>Academic Semester</th>
                        <th>Advisor</th>
                        <th>Examiner</th>
                        <th>Defense Room</th>
                        <th>Defense Date</th>
                        <th>Defense Time</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var defense_list_table = $('table#defense_list_table').DataTable({
    // ordering: false,
    paging: false,
    ajax:{
        url: '<?=base_url()?>thesis/get_thesis_defense',
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#form_filter_defense').serialize();
            return a_form_data;
            // return false
        }
    },
    columns: [
        {
            data: 'personal_data_name'
        },
        {data: 'student_number'},
        {data: 'study_program_abbreviation'},
        {
            data: 'academic_year_id',
            render: function(data, type, row) {
                return row.defense_academic_year_id + row.defense_semester_type_id;
            }
        },
        {data: 'advisors'},
        {data: 'examiners'},
        {data: 'thesis_defense_room'},
        {data: 'defense_date'},
        {data: 'thesis_defense_time'},
        {
            data: 'thesis_defense_id',
            render: function(data, type, row) {
                var listfile_thesis_work = row.thesis_log_work_files;
                // console.log(listfile_thesis_work);
                var html = '<div class="btn-group btn-group-sm" role="group">';
                html += '<button id="btnGrouView" type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">View File</button>';
                html += '<div class="dropdown-menu" aria-labelledby="btnGrouView">';
                
                if (listfile_thesis_work.length > 0) {
                    for (let i = 0; i < listfile_thesis_work.length; i++) {
                        var listfile = listfile_thesis_work[i];
                        html += '<a class="dropdown-item" id="btn_view_' + listfile.filename_button + '" href="<?=base_url()?>thesis/view_file/thesis_work/' + row.thesis_student_id + '/' + listfile.thesis_filename + '" target="_blank">' + listfile.thesis_filename + '</a>';
                    }
                }

                html += '</div>';

                if (row.thesis_activity) {
                    html += '<a href="<?=base_url()?>academic/activity_study/activity_study_list/' + row.thesis_activity + '" class="btn btn-primary btn-sm" target="_blank">></a>';
                }
                else {
                    html += '<button type="button" id="btn_push_activity" class="btn btn-primary btn-sm"><i class="fas fa-flag"></i></button>';
                }

                html += '</div>';
                return html;
            }
        },
    ]
});

var button_form_attendance = $('button#attendance_form');
var button_form_twe = $('button#twe_form');
var button_form_tpe = $('button#tpe_form');
var button_form_score = $('button#score_form');
var button_examiner_settings = $('button#examiner_settings');
var button_view_file = $('button#btnGrouView');

var dtnow = new Date($.now());

$(function() {
    $('table#defense_list_table tbody').on('click', 'button#btn_push_activity', function(params) {
        var row_data = defense_list_table.row($(this).parents('tr')).data();
        $.blockUI({ baseZ: 2000 });

        $.post('<?=base_url()?>academic/activity_study/from_defense', {defense_id: row_data.thesis_defense_id}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                defense_list_table.ajax.reload();
                toastr.success('Success');
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error procesing data to server!');
        });
    })
    defense_list_table.on('select', function(e, dt, type, indexes) {
        var row_data = defense_list_table.row(indexes).data();
        var user_type = row_data.user_type;
        console.log(user_type);
        var tooltips_default = 'Please select one record from the Thesis Defense List';
        // var tolltips_approved = 'Thesis with ' + row_data.current_status + ' status can not processed with this action';
        var link_docs = '<?=base_url()?>thesis/view_file/thesis_work/' + row_data.thesis_student_id + '/';
        var link_docs_final = '<?=base_url()?>thesis/view_file/thesis_final/' + row_data.thesis_student_id + '/';

        $('#btn_view_work_file').addClass('disabled');
        $('#btn_view_work_plagiate_check').addClass('disabled');
        $('#btn_view_work_log').addClass('disabled');
        $('#btn_view_work_other_doc').addClass('disabled');

        if (user_type == 'examiner') {
            button_form_tpe.removeAttr('disabled');
            button_form_tpe.tooltip('disable');

            button_form_score.removeAttr('disabled');
            button_form_score.tooltip('disable');
        }
        else if (user_type == 'advisor') {
            button_form_attendance.removeAttr('disabled');
            button_form_twe.removeAttr('disabled');
            button_form_tpe.removeAttr('disabled');
            button_form_score.removeAttr('disabled');

            button_form_attendance.tooltip('disable');
            button_form_twe.tooltip('disable');
            button_form_tpe.tooltip('disable');
            button_form_score.tooltip('disable');
        }
        else if (user_type == 'deans') {
            button_form_score.removeAttr('disabled');
            button_form_score.tooltip('disable');
        }

        button_examiner_settings.removeAttr('disabled');
        button_examiner_settings.tooltip('disable');
        button_view_file.removeAttr('disabled');
        button_view_file.tooltip('disable');

        var row_files = row_data.thesis_log_work_files;
        if (row_files) {
            $.each(row_files, function(i, v) {
                $('#btn_view_' + v.filename_button).removeClass('disabled');
                $('#btn_view_' + v.filename_button).attr('href', link_docs + v.thesis_filename);
            })
        }
        // if ((row_data.thesis_work_fname != '') && (row_data.thesis_work_fname != null)) {
        //     $('#btn_view_work_file').removeClass('disabled');
        //     $('#btn_view_work_file').attr('href', link_docs + row_data.thesis_work_fname);
        // }
        // if ((row_data.thesis_plagiate_check_fname != '') && (row_data.thesis_plagiate_check_fname != null)) {
        //     $('#btn_view_work_plagiate_check').removeClass('disabled');
        //     $('#btn_view_work_plagiate_check').attr('href', link_docs + row_data.thesis_plagiate_check_fname);
        // }
        // if ((row_data.thesis_log_fname != '') && (row_data.thesis_log_fname != null)) {
        //     $('#btn_view_work_log').removeClass('disabled');
        //     $('#btn_view_work_log').attr('href', link_docs + row_data.thesis_log_fname);
        // }
        // if ((row_data.thesis_other_doc_fname != '') && (row_data.thesis_other_doc_fname != null)) {
        //     $('#btn_view_work_other_doc').removeClass('disabled');
        //     $('#btn_view_work_other_doc').attr('href', link_docs + row_data.thesis_other_doc_fname);
        // }
        // if ((row_data.thesis_final_fname != '') && (row_data.thesis_final_fname != null)) {
        //     $('#btn_view_final_thesis').removeClass('disabled');
        //     $('#btn_view_final_thesis').attr('href', link_docs_final + row_data.thesis_final_fname);
        // }

    }).on('deselect', function(e, dt, type, indexes) {
        button_form_attendance.attr('disabled', 'disabled');
        button_form_twe.attr('disabled', 'disabled');
        button_form_tpe.attr('disabled', 'disabled');
        button_form_score.attr('disabled', 'disabled');
        button_examiner_settings.attr('disabled', 'disabled');
        button_view_file.attr('disabled', 'disabled');

        button_form_attendance.tooltip('enable');
        button_form_twe.tooltip('enable');
        button_form_tpe.tooltip('enable');
        button_form_score.tooltip('enable');
        button_examiner_settings.tooltip('enable');
        button_view_file.tooltip('enable');
    });

    $('button#publish_all_score').on('click', function(e) {
        e.preventDefault();

        if (confirm('Publish all score ?')) {
            $.blockUI();
            $.post('<?=base_url()?>thesis/force_publish_all_score', function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        }
    });

    $('#btn_filter_defense').on('click', function(e) {
        e.preventDefault();

        defense_list_table.ajax.reload();
    })
    
    $('button#attendance_form').on('click', function(e) {
        e.preventDefault();

        var checked = defense_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Defense List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                var dtdefense = data.thesis_defense_date + ' ' + data.thesis_defense_time_start;
                var dtdefense_end = data.thesis_defense_date + ' 23:59:59';
                let dtdeff = Date.parse(dtdefense);
                let dtdeff_end = Date.parse(dtdefense_end);

                var diff = new Date(dtdeff - dtnow);
                var diff_end = new Date(dtdeff_end - dtnow);
                var minute = diff/1000/60;
                var days = diff_end/1000/60/60/24;

                var allow_update = true;

                if (minute > 0) {
                    if ('<?=$this->session->userdata('user')?>' != '37b0f8e9-e13c-4104-adea-6c83ca1f5855') {
                        // toastr.warning('Absences can only be opened after the defense start until midnight!');
                        allow_update = false;
                    }
                }
                else if (days < 0) {
                    if ('<?=$this->session->userdata('user')?>' != '37b0f8e9-e13c-4104-adea-6c83ca1f5855') {
                        // toastr.warning('Absences can only be opened after the defense start until midnight!');
                        allow_update = false;
                    }
                }
                
                if (allow_update) {
                    $('#open_absence').attr('href', '<?=base_url()?>thesis/form_attendance/' + data.thesis_defense_id);
                    $('#open_absence')[0].click();
                }
                else {
                    toastr.warning('Absences can only be opened after the defense start untils midnight!');
                }
            }else {
                toastr.warning('Please select one record from the Thesis Defense Lists', 'Warning!');
            }
        }
    });

    $('#list_advisor_examiner tbody').on('change', 'input.check_score', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        var data_list = list_advisor_examiner.row($(this).parents('tr')).data();
        var tpe_score = data_list.tpe_score[0];
        var twe_score = data_list.twe_score[0];
        var checkbox_type = $(this).attr('check-type');
        var data_key = '';
        var data_id = '';
        
        var data_source = (checkbox_type == 'tpe') ? tpe_score : twe_score;
        var data_key = (checkbox_type == 'tpe') ? 'score_presentation_id' : 'score_evaluation_id';
        var data_id = (checkbox_type == 'tpe') ? tpe_score.score_presentation_id : twe_score.score_evaluation_id;

        if (data_source.score_status == 'open') {
            $.unblockUI();
            toastr.warning('Status is open, cannt close sign from this feature!');
        }
        else {
            $.unblockUI();
            // console.log(tpe_score);
            $.post('<?=base_url()?>thesis/open_score', {key: data_key, key_id: data_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
            list_advisor_examiner.ajax.reload();
        }
    });

    $('button#examiner_settings').on('click', function(e) {
        e.preventDefault();

        var checked = defense_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Defense List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                
                $('input#thesis_defense_id').val(data.thesis_defense_id)
                $('#modal_advisor_settings').modal('show');
                list_advisor_examiner.ajax.reload();
            }
            else {
                toastr.warning('Please select one record from the Thesis Defense Lists', 'Warning!');
            }
        }
    });
    
    $('button#twe_form').on('click', function(e) {
        e.preventDefault();

        var checked = defense_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Defense List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                // var dtdefense = data.thesis_defense_date + ' ' + data.thesis_defense_time_start;
                // var dtdefense_end = data.thesis_defense_date + ' 23:59:59';
                // let dtdeff = Date.parse(dtdefense);
                // let dtdeff_end = Date.parse(dtdefense_end);

                // var diff = new Date(dtdeff - dtnow);
                // var diff_end = new Date(dtdeff_end - dtnow);
                // var minute = diff/1000/60;
                // var days = diff_end/1000/60/60/24;

                // if (minute > 0) {
                //     toastr.warning('Thesis Work Evaluation can only be opened after the defense start until midnight!');
                // }
                // else if (days < 0) {
                //     toastr.warning('Thesis Work Evaluation can only be opened after the defense start until midnight!');
                // }
                // else {
                    $('#open_absence').attr('href', '<?=base_url()?>thesis/form_twe/' + data.thesis_defense_id);
                    $('#open_absence')[0].click();
                // }
            }else {
                toastr.warning('Please select one record from the Thesis Defense Lists', 'Warning!');
            }
        }
    });
    
    $('button#score_form').on('click', function(e) {
        e.preventDefault();

        var checked = defense_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Defense List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                
                $('#open_absence').attr('href', '<?=base_url()?>thesis/form_score/' + data.thesis_defense_id);
                $('#open_absence')[0].click();
            }else {
                toastr.warning('Please select one record from the Thesis Defense Lists', 'Warning!');
            }
        }
    });

    $('button#tpe_form').on('click', function(e) {
        e.preventDefault();

        var checked = defense_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Defense List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                // console.log(data);
                var dtdefense = data.thesis_defense_date + ' ' + data.thesis_defense_time_start;
                var dtdefense_end = data.thesis_defense_date + ' 23:59:59';
                let dtdeff = Date.parse(dtdefense);
                let dtdeff_end = Date.parse(dtdefense_end);

                var diff = new Date(dtdeff - dtnow);
                var diff_end = new Date(dtdeff_end - dtnow);
                var minute = (diff/1000/60) - 60;
                var days = diff_end/1000/60/60/24;
                console.log(minute);

                // if (minute > 0) {
                //     toastr.warning('Thesis Presentation Evaluation can only be opened after the defense start until midnight!');
                // }
                // else if (days < 0) {
                //     toastr.warning('Thesis Presentation Evaluation can only be opened after the defense start until midnight!');
                // }
                // else {
                    $('#open_absence').attr('href', '<?=base_url()?>thesis/form_tpe/' + data.thesis_defense_id);
                    $('#open_absence')[0].click();
                // }
            }else {
                toastr.warning('Please select one record from the Thesis Defense Lists', 'Warning!');
            }
        }
    });
})
</script>