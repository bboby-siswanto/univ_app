<div class="row mb-2">
    <div class="col-sm-6 col-md-3">
        
        <!-- <div class="input-group"> -->
            <select name="period_defense" id="period_defense" class="form-control">
                <option value="all">All</option>
    <?php
    if (isset($academic_year_list) AND (count($academic_year_list) > 0)) {
        foreach ($academic_year_list as $o_academic_year) {
            $s_period_active = $this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active');
            $selected_odd = ($s_period_active == $o_academic_year->academic_year_id.'1') ? 'selected=""' : '';
            $selected_even = ($s_period_active == $o_academic_year->academic_year_id.'2') ? 'selected=""' : '';
    ?>
                <option value="<?=$o_academic_year->academic_year_id.'-1';?>" <?=$selected_odd;?>><?=$o_academic_year->academic_year_id.' Odd';?></option>
                <option value="<?=$o_academic_year->academic_year_id.'-2';?>" <?=$selected_even;?>><?=$o_academic_year->academic_year_id.' Even';?></option>
    <?php
        }
    }
    ?>
            </select>
        <!-- </div> -->
    </div>
<?php
if (in_array($this->session->userdata('user'), ['37b0f8e9-e13c-4104-adea-6c83ca1f5855', '47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
?>
    <div class="col-sm-6 col-md-6">
        <div class="btn-group btn-group-sm float-right mb-2" role="group" aria-label="Basic example">
            <a href="<?=base_url()?>thesis/manage_defense/<?=$academic_year_id;?><?=$semester_type_id;?>" class="btn btn-sm btn-primary">Manage Defense</a>
        </div>
    </div>
<?php
}
?>
</div>
<div class="card">
    <div class="card-header">
        Student List of Thesis Defense
        <a href="<?=base_url()?>" id="open_absence"></a>
        <div class="card-header-actions">
            <!-- <button class="card-header-action btn btn-link" id="publish_all_score">
                <i class="fa fa-eye"></i> Publish All Score
            </button> -->
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="btn-toolbar" role="toolbar" aria-label="Action button groups">
                    <div class="btn-group mr-2" role="group" aria-label="First group">
                        <button type="button" class="btn btn-primary" id="attendance_form" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Defense List" disabled>
                            1. Attendance Form
                        </button>
                        <button type="button" class="btn btn-primary" id="twe_form" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Defense List" disabled>
                            2. Thesis Work Evaluation
                        </button>
                        <button type="button" class="btn btn-primary" id="tpe_form" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Defense List" disabled>
                            3. Thesis Presentation Evaluation
                        </button>
                        <button type="button" class="btn btn-primary" id="score_form" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Defense List" disabled>
                            4. View Score Result
                        </button>
<?php
if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
?>
                        <button type="button" class="btn btn-success" id="examiner_settings" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Defense List" disabled>
                            <i class="fas fa-cog"></i> Setting Examiner / Advisor
                        </button>
<?php
}
?>
                    </div>
                    <div class="btn-group mr-2 float-right" role="group" aria-label="First group">
                        <div class="btn-group" role="group">
                            <button id="btnGrouView" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Defense List" disabled>
                                View File
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGrouView">
                                <a class="dropdown-item disabled" id="btn_view_work_file" href="#" target="_blank">Thesis Work</a>
                                <a class="dropdown-item disabled" id="btn_view_work_plagiate_check" href="#" target="_blank">Thesis Plagiarism Check</a>
                                <a class="dropdown-item disabled" id="btn_view_work_log" href="#" target="_blank">Thesis Log</a>
                                <a class="dropdown-item disabled" id="btn_view_work_other_doc" href="#" target="_blank">Other Required Documents</a>
                                <!-- <a class="dropdown-item disabled" id="btn_view_final_thesis" href="#" target="_blank">Final Thesis (after defense)</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table id="defense_list_table" class="table table-bordered table-hover">
            <thead class="bg-dark">
                <tr>
                    <th></th>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Study Program</th>
                    <th>Defense Room</th>
                    <th>Defense Date</th>
                    <th>Defense Time</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_advisor_settings">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setting Open / Closed Sign Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="thesis_defense_id" id="thesis_defense_id">
                <table id="list_advisor_examiner" class="table">
                    <thead class="bg-dark">
                        <tr>
                            <th>Name</th>
                            <th>Advisor / Examiner</th>
                            <th>Sign TWE</th>
                            <th>Sign TPE</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
var list_advisor_examiner = $('table#list_advisor_examiner').DataTable({
    ordering: false,
    paging: false,
    info: false,
    ajax:{
        url: '<?=base_url()?>thesis/get_advisor_examiner',
        type: 'POST',
        data: function(d) {
            d.thesis_defense_id = $('#thesis_defense_id').val();
        }
    },
    columns: [
        {
            data: 'user_name',
        },
        {
            data: 'user_type'
        },
        {
            data: 'thesis_student_id',
            render: function(data, type, row) {
                var sign = '';
                if (row.student_examiner_id === null) {
                    if (row.twe_score) {
                        score_twe = row.twe_score[0];
                        sign = '<div class="custom-control custom-switch">';
                        if (score_twe.score_status == 'closed') {
                            sign += '<input class="custom-control-input toggle_sign check_score" check-type="twe" type="checkbox" id="advisor_twe_' + score_twe.score_evaluation_id + '" name="advisor_twe_' + score_twe.score_evaluation_id + '" checked>';
                        }
                        else {
                            sign += '<input class="custom-control-input toggle_sign check_score" check-type="twe" type="checkbox" id="advisor_twe_' + score_twe.score_evaluation_id + '" name="advisor_twe_' + score_twe.score_evaluation_id + '">';
                        }
                        sign += '<label class="custom-control-label" for="advisor_twe_' + score_twe.score_evaluation_id + '"></label>';
                        sign += '</div>';
                    }
                    // var sign = 'advisor';
                }
                return sign;
            }
        },
        {
            data: 'thesis_student_id',
            render: function(data, type, row) {
                var sign = '';
                if (row.tpe_score) {
                    score_tpe = row.tpe_score[0];
                    sign = '<div class="custom-control custom-switch">';
                    if (score_tpe.score_status == 'closed') {
                        sign += '<input class="custom-control-input toggle_sign check_score" check-type="tpe" type="checkbox" id="advisor_tpe_' + score_tpe.score_presentation_id + '" name="advisor_tpe_' + score_tpe.score_presentation_id + '" checked>';
                    }
                    else {
                        sign += '<input class="custom-control-input toggle_sign check_score" check-type="tpe" type="checkbox" id="advisor_tpe_' + score_tpe.score_presentation_id + '" name="advisor_tpe_' + score_tpe.score_presentation_id + '">';
                    }
                    sign += '<label class="custom-control-label" for="advisor_tpe_' + score_tpe.score_presentation_id + '"></label>';
                    sign += '</div>';
                }
                return sign;
            }
        }
    ],
});

var defense_list_table = $('table#defense_list_table').DataTable({
    ordering: false,
    processing: true,
    paging: false,
    ajax:{
        url: '<?=base_url()?>thesis/get_thesis_defense',
        type: 'POST',
        data: function(params) {
            var period = $('#period_defense').val();
            var periode = period.split('-');
            params.academic_year_defense = periode[0];
            params.semester_type_defense = periode[1];
            // let a_form_data = $('form#form_filter_thesis_work').serialize();
            // return a_form_data;
            return params;
        }
    },
    columns: [
        {
            data: 'thesis_defense_id',
            className: 'select-checkbox',
            render: function(data, type, row) {
                var html = '<input type="hidden" value="' + data + '" name="thesis_id">';
                return html;
            }
        },
        {
            data: 'personal_data_name'
        },
        {data: 'student_number'},
        {data: 'study_program_abbreviation'},
        {data: 'thesis_defense_room'},
        {data: 'defense_date'},
        {data: 'thesis_defense_time'},
    ],
    select: {
        style: 'single'
    }
});

var button_form_attendance = $('button#attendance_form');
var button_form_twe = $('button#twe_form');
var button_form_tpe = $('button#tpe_form');
var button_form_score = $('button#score_form');
var button_examiner_settings = $('button#examiner_settings');
var button_view_file = $('button#btnGrouView');

var dtnow = new Date($.now());

$(function() {
    $('#period_defense').select2();
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
        console.log(row_data);
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

    $('#period_defense').on('change', function(e) {
        defense_list_table.ajax.reload();
    })

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