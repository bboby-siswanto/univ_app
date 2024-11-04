<style>
.ui-autocomplete {
    max-height: 180px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    z-index:2147483647 !important;
}
</style>
<div class="card">
    <div class="card-header">
        Filter Data
    </div>
    <div class="card-body">
        <form url="<?=base_url()?>thesis/get_thesis_list" id="form_filter_thesis_work" onsubmit="return false">
            <input type="hidden" name="current_progress" value="work">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="study_program_id_filter">Study Program</label>
                        <select name="study_program_id" id="study_program_id_filter" class="form-control">
                            <option value="">All</option>
    <?php
    if (($study_program_list)) {
        foreach ($study_program_list as $o_prodi) {
    ?>
                            <option value="<?=$o_prodi->study_program_id;?>"><?=$o_prodi->study_program_name;?></option>
    <?php
        }
    }
    ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="academic_year_id_filter">Academic Year</label>
                        <select name="academic_year_id" id="academic_year_id_filter" class="form-control">
    <?php
    if (($academic_year_list)) {
        foreach ($academic_year_list as $o_year) {
            $selected = ($o_year->academic_year_id == $this->session->userdata('academic_year_id_active')) ? 'selected = "selected"' : '';
    ?>
                            <option value="<?=$o_year->academic_year_id;?>" <?=$selected;?>><?=$o_year->academic_year_id.'/'.(intval($o_year->academic_year_id) + 1);?></option>
    <?php
        }
    }
    ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="status_filter">Status</label>
                        <select name="status" id="status_filter" class="form-control">
                            <option value="">All</option>
    <?php
    if ($status_list) {
        foreach ($status_list as $key => $value) {
            $selected = ($key == 'approved') ? 'selected = "selected"' : '';
    ?>
                            <option value="<?=$key;?>" <?=$selected;?>><?= ucwords(strtolower($value));?></option>
    <?php
        }
    }
    ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="semester_type_id_filter">Semester Type</label>
                        <select name="semester_type_id" id="semester_type_id_filter" class="form-control">
    <?php
    if (($semester_type_list)) {
        foreach ($semester_type_list as $o_semester) {
            $selected = ($o_semester->semester_type_id == $this->session->userdata('semester_type_id_active')) ? 'selected = "selected"' : '';
            // $selected = ($o_semester->semester_type_id == 2) ? 'selected = "selected"' : '';
    ?>
                            <option value="<?=$o_semester->semester_type_id;?>" <?=$selected;?>><?=$o_semester->semester_type_name;?></option>
    <?php
        }
    }
    ?>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <button class="float-right btn btn-primary" type="button" id="submit_filter_data">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Thesis Work Submission List
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 pb-3">
                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
<?php
if ((in_array($access_thesis, ['deans', 'hsp'])) OR (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855','150ba76c-4dd2-46e2-bf67-60123b0e2ce6']))) {
?>
                    <div class="btn-group mr-2" role="group" aria-label="First group">
                        <button type="button" class="btn btn-primary" id="update_tw" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Work Submission List">
                            Update
                        </button>
                        <button type="button" class="btn btn-primary" id="approve_tw" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Work Submission List" disabled>
                            Approve
                        </button>
                        <button type="button" class="btn btn-primary" id="reject_tw" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Work Submission List" disabled>
                            Reject
                        </button>
                    </div>
<?php
}
?>
                    <div class="btn-group mr-2" role="group" aria-label="Second group">
                        <div class="btn-group" role="group">
                            <button id="btnGrouView" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                View
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGrouView">
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_work_file" href="#" target="_blank">Thesis Work</a>
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_work_plagiate_check" href="#" target="_blank">Thesis Plagiarism Check</a>
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_work_log" href="#" target="_blank">Thesis Log</a>
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_work_other_doc" href="#" target="_blank">Other Required Documents</a>
                            </div>
                        </div>
<?php
if ((in_array($access_thesis, ['deans'])) OR (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855','150ba76c-4dd2-46e2-bf67-60123b0e2ce6']))) {
// if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
?>
                        <button type="button" class="btn btn-primary" id="btn_add_schedule" data-toggle="tooltip" data-placement="bottom" title="Please select one record from the Thesis Work Submission List">
                            Add Schedule
                        </button>
<?php
}
?>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" id="submission_list_table">
                        <thead class="bg-dark">
                            <tr>
                                <th></th>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th>Student Email</th>
                                <th>Prodi</th>
                                <th>Batch</th>
                                <th>Thesis Title</th>
                                <th>Advisor</th>
                                <th>Current Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_update_thesis_work">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_update_thesis"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= modules::run('thesis/form_edit_thesis_work');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_note_action">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remark_type"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_remarks" onsubmit="return false">
                    <button id="new_remarks_action" class="btn btn-primary" type="button"><i class="fas fa-plus"></i> Add Note</button>
                    <input type="hidden" name="action_submit" id="action_submit" value="">
                    <table id="table_remarks" class="table">
                        <thead>
                            <tr>
                                <th style="width: 5% !important">#</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='btn_submit_action'>Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_schedule_defense">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Schedule to Defense <span id="sc_title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_schedule" url="<?=base_url()?>thesis/submit_schedule" method="POST" onsubmit="return false">
                    <input type="hidden" name="sc_thesis_student_id" id="sc_thesis_student_id" class="v_value">
                    <div class="form-group">
                        <label id="sc_thesis_title"></label>
                    </div>
                    <div class="form-group">
                        <label for="">Semester Academic</label>
                        <div class="input-group">
                            <!-- <input type="text" class="form-control" name="sc_academic_year_id" id="sc_academic_year_id"> -->
                            <select name="sc_academic_year_id" id="sc_academic_year_id" class="form-control">
                                <option value=""></option>
                    <?php
                    if ((isset($academic_year_list)) AND ($academic_year_list)) {
                        foreach ($academic_year_list as $o_year) {
                    ?>
                                <option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?></option>
                    <?php
                        }
                    }
                    ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="sc_semester_type_id" id="sc_semester_type_id"> -->
                            <select name="sc_semester_type_id" id="sc_semester_type_id" class="form-control">
                                <option value="1">ODD</option>
                                <option value="2">EVEN</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sc_room">Room</label>
                        <select name="sc_room" id="sc_room" class="form-control v_value">
                            <option value="R702">R702</option>
                            <option value="R703">R703</option>
                            <option value="R704">R704</option>
                            <option value="R716">R716</option>
                            <option value="R717">R717</option>
                            <option value="R718">R718</option>
                            <option value="R719">R719</option>
                            <option value="R720">R720</option>
                            <option value="R721">R721</option>
                            <option value="R722">R722</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sc_date">Date</label>
                        <input type="date" name="sc_date" id="sc_date" class="form-control v_value">
                    </div>
                    <div class="form-group">
                        <label for="sc_time_start">Time</label>
                        <div class="row">
                            <div class="col-5">
                                <select name="sc_time_start" id="sc_time_start" class="form-control v_value">
                        <?php
                        for ($i=8; $i <=21 ; $i++) { 
                            $text_start = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':00:00' : $i.':00:00';
                            $text_start_15 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':15:00' : $i.':15:00';
                            $text_start_30 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':30:00' : $i.':30:00';
                            $text_start_45 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':45:00' : $i.':45:00';
                        ?>
                                    <option value="<?=$text_start;?>"><?= $text_start?></option>
                                    <option value="<?=$text_start_15;?>"><?= $text_start_15?></option>
                                    <option value="<?=$text_start_30;?>"><?= $text_start_30?></option>
                                    <option value="<?=$text_start_45;?>"><?= $text_start_45?></option>
                        <?php
                        }
                        ?>
                                </select>
                            </div>
                            <div class="col-2 text-center"> - </div>
                            <div class="col-5">
                                <select name="sc_time_end" id="sc_time_end" class="form-control v_value">
                        <?php
                        for ($i=9; $i <=22 ; $i++) {
                            $text_end = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':00:00' : $i.':00:00';
                            $text_end_15 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':15:00' : $i.':15:00';
                            $text_end_30 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':30:00' : $i.':30:00';
                            $text_end_45 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':45:00' : $i.':45:00';
                        ?>
                                    <option value="<?=$text_end;?>"><?= $text_end?></option>
                                    <option value="<?=$text_end_15;?>"><?= $text_end_15?></option>
                                    <option value="<?=$text_end_30;?>"><?= $text_end_30?></option>
                                    <option value="<?=$text_end_45;?>"><?= $text_end_45?></option>
                        <?php
                        }
                        ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='btn_submit_schedule'>Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_add_advisor">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Advisor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_new_advisor" url="<?=base_url()?>thesis/new_advisor" method="POST" onsubmit="return false">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Advisor Name</label>
                                <input type="text" name="advisor_personal_data_name" id="advisor_personal_data_name" class="form-control v_value">
                                <input type="hidden" name="advisor_personal_data_id" id="advisor_personal_data_id" class="v_value">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Institution</label>
                                <input type="text" name="advisor_institution_name" id="advisor_institution_name" class="form-control v_value">
                                <input type="hidden" name="advisor_institution_id" id="advisor_institution_id" class="v_value">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_new_advisor">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var users = '<?=$access_thesis;?>';
    var counter = 1;
    // console.log(type);
    var table_remarks = $('#table_remarks').DataTable({
        order: false,
        sort: false,
        info: false,
        paging: false,
        searching: false,
        autoWidth: false,
        columnDefs: [
            { "width": "10px", "targets": 0 }
        ]
    });

    $('#sc_time_start').on('change', function(e) {
        e.preventDefault();

        var idx_selected = $('#sc_time_start')[0].selectedIndex;
        $('#sc_time_end :nth-child(' + (idx_selected + 2) + ')').prop('selected', true);
    });

    $('#modal_note_action').on('hidden.bs.modal', function (e) {
        counter = 1;
    });
    
    var submission_list_table = $('table#submission_list_table').DataTable({
        order: [[1, "asc"]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-files-o"></i> Copy',
                title: 'Student Thesis Submission',
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                messageTop: 'Student Thesis Submission'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-files-o"></i> CSV',
                title: 'Student Thesis Submission',
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                messageTop: 'Student Thesis Submission'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-files-o"></i> Excel',
                title: 'Student Thesis Submission',
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                messageTop: 'Student Thesis Submission'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-files-o"></i> Pdf',
                title: 'Student Thesis Submission',
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                messageTop: 'Student Thesis Submission'
            },
            'colvis'
        ],
        ajax:{
            url: '<?=base_url()?>thesis/get_thesis_list',
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#form_filter_thesis_work').serialize();
                return a_form_data;
            }
        },
        columns: [
            {
                data: 'thesis_student_id',
                orderable: false,
                className: 'select-checkbox',
                render: function(data, type, row) {
                    var html = '<input type="hidden" value="' + data + '" name="thesis_student_id">';
                    return html;
                }
            },
            {data: 'personal_data_name'},
            {data: 'student_number'},
            {data: 'student_email'},
            {data: 'study_program_abbreviation'},
            {data: 'student_batch'},
            {data: 'thesis_title'},
            {data: 'advisor_approved'},
            // {data: 'advisor_2'},
            {
                data: 'current_status',
                render: function(data, type, row) {
                    var html = '<label class="badge badge-pill badge-warning">' + data + '</label>';
                    switch (data) {
                        case 'approved_hsp':
                            html = '<label class="badge badge-pill badge-info">' + data + '</label>';
                            break;

                        case 'approved':
                            html = '<label class="badge badge-pill badge-success">' + data + '</label>';
                            break;

                        case 'rejected':
                            html = '<label class="badge badge-pill badge-danger">' + data + '</label>';
                            break;

                        case 'revision':
                            var html = '<label class="badge badge-pill badge-secondary">' + data + '</label>';
                            break;
                    
                        default:
                            break;
                    }
                    html += '<span id="data_id" class="d-none">' + row.thesis_student_id + '</span>';
                    return html;
                }
            },
        ],
        select: {
            style: 'single'
        }
    });

    $('button#btn_add_schedule').on('click', function(e) {
        e.preventDefault();

        // thesis_student_id
        var checked = submission_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                let defense_data = data.defense_data;
                console.log(defense_data);
                $('.v_value').val('');
                $('#sc_title').html(data.personal_data_name + ' (' + data.study_program_abbreviation + '/' + data.student_batch + ')');
                $('#sc_thesis_title').text(data.thesis_title);
                $('#sc_thesis_student_id').val(data.thesis_student_id);

                if (defense_data) {
                    $('#sc_academic_year_id').val(defense_data.defense_academic_year_id);
                    $('#sc_semester_type_id').val(defense_data.defense_semester_type_id);
                    $('#sc_date').val(defense_data.thesis_defense_date);
                    $('#sc_room').val(defense_data.thesis_defense_room);
                    $('#sc_time_start').val(defense_data.thesis_defense_time_start);
                    $('#sc_time_end').val(defense_data.thesis_defense_time_end);
                }

                $('#modal_schedule_defense').modal('show');
            }else {
                toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
            }
        }
    });

    $('button#new_remarks_action').on( 'click', function () {
        table_remarks.row.add([
            counter,
            '<textarea name="remarks_data[]" class="form-control"></textarea>'
        ] ).draw( false );

        counter++;
    } );

    $('button#submit_filter_data').on('click', function(e) {
        e.preventDefault();
        submission_list_table.ajax.reload(null, false);
    });

    $('button#btn_submit_schedule').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_schedule');
        var url = form.attr('url');
        var data = form.serialize();
        $.post(url, data, function(result) {
            if (result.code == 0) {
                submission_list_table.ajax.reload(null, false);
                toastr.success('Success!');
                $('#form_schedule').find('input, select').val('');
                $('#modal_schedule_defense').modal('hide');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(param) {
            toastr.error('Error processing data!');
        });
    });

    submission_list_table.on('select', function(e, dt, type, indexes) {
        var row_data = submission_list_table.row(indexes).data();
        var tooltips_default = 'Please select one record from the Thesis Work Submission List';
        var tolltips_approved = 'Thesis with ' + row_data.current_status + ' status can not processed with this action';
        var link_docs = '<?=base_url()?>thesis/view_file/thesis_work/' + row_data.thesis_student_id + '/';

        var button_update_tw = $('button#update_tw');
        var button_approve_tw = $('button#approve_tw');
        var button_reject_tw = $('button#reject_tw');

        var btn_view_thesis_work = $('#btn_view_work_file');
        var btn_view_plagiarism = $('#btn_view_work_plagiate_check');
        var btn_view_thesis_log = $('#btn_view_work_log');
        var btn_view_other_doc = $('#btn_view_work_other_doc');
        var btn_add_schedule = $('#btn_add_schedule');
        
        $('button#update_tw').attr('title', tolltips_approved).attr('data-original-title', tolltips_approved);
        $('button#approve_tw').attr('title', tolltips_approved).attr('data-original-title', tolltips_approved);
        $('button#reject_tw').attr('title', tolltips_approved).attr('data-original-title', tolltips_approved);

        $('button#update_tw').tooltip('enable');
        $('button#approve_tw').tooltip('enable');
        $('button#reject_tw').tooltip('enable');
        
        if ((users == 'hsp') && (row_data.current_status == 'pending')) {
            button_update_tw.removeAttr('disabled');
            button_approve_tw.removeAttr('disabled');
            
            button_approve_tw.tooltip('disable');
            button_update_tw.tooltip('disable');
        }
        else if ((users == 'deans') && (row_data.current_status == 'pending')) {
            button_update_tw.removeAttr('disabled');
            button_approve_tw.removeAttr('disabled');
            button_reject_tw.removeAttr('disabled');

            button_approve_tw.tooltip('disable');
            button_update_tw.tooltip('disable');
            button_reject_tw.tooltip('disable');
        }
        else if ((users == 'deans') && (row_data.current_status == 'approved_hsp')) {
            button_update_tw.removeAttr('disabled');
            button_approve_tw.removeAttr('disabled');
            button_reject_tw.removeAttr('disabled');
            
            button_update_tw.tooltip('disable');
            button_approve_tw.tooltip('disable');
            button_reject_tw.tooltip('disable');
        }
        else if (row_data.current_status == 'approved') {
            if (('<?=$this->session->userdata('user');?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') || ('<?=$this->session->userdata('user');?>' == '37b0f8e9-e13c-4104-adea-6c83ca1f5855')) {
                button_update_tw.removeAttr('disabled');
                button_update_tw.tooltip('disable');
            }
            btn_add_schedule.removeAttr('disabled');
            btn_add_schedule.tooltip('disable');
        }
        else if (row_data.current_status == 'approved_hsp') {
            btn_add_schedule.removeAttr('disabled');
            btn_add_schedule.tooltip('disable');
        }

        // console.log(row_data.thesis_log_files);
        var row_files = row_data.thesis_log_files;
        $('.thesis_file_button').addClass('disabled');
        if (row_files) {
            $.each(row_files, function(i, v) {
                $('#btn_view_' + v.filename_button).removeClass('disabled');
                $('#btn_view_' + v.filename_button).attr('href', link_docs + v.thesis_filename);
            })
        }

        // thesis_work_file
        // thesis_work_plagiate_check
        // thesis_work_log
        // work_other_doc

        // var btn_view_thesis_work = $('#btn_view_work_file');
        // var btn_view_plagiarism = $('#btn_view_work_plagiate_check');
        // var btn_view_thesis_log = $('#btn_view_work_log');
        // var btn_view_other_doc = $('#btn_view_work_other_doc');
        // var btn_add_schedule = $('#btn_add_schedule');

        // if ((row_data.thesis_work_fname != '') && (row_data.thesis_work_fname != null)) {
        //     btn_view_thesis_work.removeClass('disabled');
        //     btn_view_thesis_work.attr('href', link_docs + row_data.thesis_work_fname);
        // }
        // if ((row_data.thesis_plagiate_check_fname != '') && (row_data.thesis_plagiate_check_fname != null)) {
        //     btn_view_plagiarism.removeClass('disabled');
        //     btn_view_plagiarism.attr('href', link_docs + row_data.thesis_plagiate_check_fname);
        // }
        // if ((row_data.thesis_log_fname != '') && (row_data.thesis_log_fname != null)) {
        //     btn_view_thesis_log.removeClass('disabled');
        //     btn_view_thesis_log.attr('href', link_docs + row_data.thesis_log_fname);
        // }
        // if ((row_data.thesis_other_doc_fname != '') && (row_data.thesis_other_doc_fname != null)) {
        //     btn_view_other_doc.removeClass('disabled');
        //     btn_view_other_doc.attr('href', link_docs + row_data.thesis_other_doc_fname);
        // }
    }).on('deselect', function(e, dt, type, indexes) {
        $('button#update_tw').attr('disabled', 'disabled');
        $('button#approve_tw').attr('disabled', 'disabled');
        $('button#reject_tw').attr('disabled', 'disabled');
        $('button#btn_add_schedule').attr('disabled', 'disabled');

        $('#btn_view_work_file').addClass('disabled');
        $('#btn_view_work_plagiate_check').addClass('disabled');
        $('#btn_view_work_log').addClass('disabled');
        $('#btn_view_work_other_doc').addClass('disabled');

        $('button#update_tw').attr('title', tooltips_default).attr('data-original-title', tooltips_default);
        $('button#approve_tw').attr('title', tooltips_default).attr('data-original-title', tooltips_default);
        $('button#reject_tw').attr('title', tooltips_default).attr('data-original-title', tooltips_default);

        $('button#update_tw').tooltip('enable');
        $('button#approve_tw').tooltip('enable');
        $('button#reject_tw').tooltip('enable');
    });

    $('button#submit_update_thesis_work').on('click', function(e) {
        $.blockUI({ baseZ: 2000 });
        var form = $('#form_edit_thesis_work');
        var url = form.attr('url');
        var data = form.serialize();
        // var data = form.serializeArray();
        // console.log(data);
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                submission_list_table.ajax.reload(null, true);
                $('#modal_update_thesis_work').modal('hide');
                toastr.success('Success!');
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('error processing data!', 'Error');
        });
    });

    $('button#update_tw').on('click', function(e) {
        e.preventDefault();
        $('#modal_update_thesis_work').modal('show');

        var checked = submission_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                let data = data_check[0];
                console.log(data);
                $('.v_value').val('');
                $('textarea#thesis_title_update').val('');

                // $('#advisor_1_update, #advisor_2_update, #examiner_1_update, #examiner_1_update, #examiner_3_update, #examiner_4_update').select2('destroy');
                // $('#advisor_1_update, #advisor_2_update, #examiner_1_update, #examiner_1_update, #examiner_3_update, #examiner_4_update').select2();
                // $('#advisor_1_update').select2('data', null);
                $('#advisor_1_update').val(null).trigger('change');
                // $('#advisor_2_update').select2('data', null);
                $('#advisor_2_update').val(null).trigger('change');
                // $('#examiner_1_update').select2('data', null);
                $('#examiner_1_update').val(null).trigger('change');
                // $('#examiner_2_update').select2('data', null);
                $('#examiner_2_update').val(null).trigger('change');
                // $('#examiner_3_update').select2('data', null);
                $('#examiner_3_update').val(null).trigger('change');
                // $('#examiner_4_update').select2('data', null);
                $('#examiner_4_update').val(null).trigger('change');

                $('#title_update_thesis').html(data.personal_data_name + ' (' + data.study_program_abbreviation + '/' + data.student_batch + ')');
                $('#thesis_title_update').val(data.thesis_title);
                $('#thesis_id').val(data.thesis_student_id);
                var number_advisor = 1;
                $.each(data.advisor_data, function(i, v) {
                    var data = {
                        text: v.advisor_name,
                        id: v.advisor_id,
                        institution_id: v.institution_id,
                        institution_name: v.institution_name
                    };
                    console.log('#advisor_' + v.number + '_update');

                    var newOption = new Option(data.text, data.id, false, false);
                    $('#advisor_' + v.number + '_update').append(newOption);
                    $('#advisor_' + v.number + '_update').val(data.id).trigger('change');
                    $('#advisor_' + v.number + '_institute_update').val(data.institution_name);
                    number_advisor++;
                });
                
                var number_examiner = 1;
                $.each(data.examiner_data, function(i, examiner) {
                    var data = {
                        text: examiner.examiner_name,
                        id: examiner.advisor_id,
                        institution_id: examiner.institution_id,
                        institution_name: examiner.institution_name
                    };

                    var newOption = new Option(data.text, data.id, false, false);
                    $('#examiner_' + examiner.number + '_update').append(newOption);
                    $('#examiner_' + examiner.number + '_update').val(data.id).trigger('change');
                    $('#examiner_' + examiner.number + '_institute_update').val(data.institution_name);
                    number_examiner++;
                });
                // console.log(data.advisor_data);
            }
            else {
                toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
            }
        }
    });

    $('button#btn_submit_action').on('click', function(e) {
        e.preventDefault();

        var checked = submission_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length == 0) {
                toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
            }
            else {
                let data = data_check[0];
                if (data_check.current_status == 'approved') {
                    toastr.warning('Thesis status is approved', 'Warning!');
                }
                else if (data_check.current_status == 'rejected') {
                    toastr.warning('Thesis status is rejected', 'Warning!');
                }
                else {
                    $.blockUI({ baseZ: 2000 });
                    var param_data = $('form#form_remarks').serialize();
                    param_data += '&thesis_id=' + data.thesis_student_id + '&thesis_log_id=' + data.thesis_log_id + '&thesis_type=work';

                    if ($('input#action_submit').val() == 'rejected') {
                        var url = '<?=base_url()?>thesis/submit_reject_thesis';
                    }
                    else {
                        var url = '<?=base_url()?>thesis/submit_approve_thesis';
                    }
                    
                    $.post(url, param_data, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            toastr.success('Success!');
                            submission_list_table.ajax.reload(null, false);
                            $('#modal_note_action').modal('hide');
                        }
                        else {
                            toastr.warning(result.message, 'Warning!');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                        toastr.error('Error processing your request!');
                    });
                }
            }
        }
    })

    $('button#approve_tw').on('click', function(e) {
        e.preventDefault();

        var checked = submission_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                $('#remark_type').html('Approval Note ' + data_check[0].personal_data_name);
                $('input#action_submit').val('approved');
                $('#btn_submit_action').text('Submit Approval');
                table_remarks.clear().draw();
                $('button#new_remarks_action').click();
                $('#modal_note_action').modal('show');
            }
            else {
                toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
            }
        }
    });

    $('button#reject_tw').on('click', function(e) {
        e.preventDefault();

        var checked = submission_list_table.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
        }
        else {
            var data_check = checked.data();
            if (data_check.length > 0) {
                $('#remark_type').html('Reject Note ' + data_check[0].personal_data_name);
                $('input#action_submit').val('rejected');
                $('#btn_submit_action').text('Submit Reject');
                table_remarks.clear().draw();
                $('button#new_remarks_action').click();
                $('#modal_note_action').modal('show');
            }
            else {
                toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
            }
        }
    });

    $('#advisor_personal_data_name').autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('personal_data/get_personal_data_by_name')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                var list = rtn.data;
                var arr = [];
                arr = $.map(list, function(m){
                    return {
                        label: m.personal_data_name,
                        value: m.personal_data_name,
                        id: m.personal_data_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            $('#advisor_personal_data_id').val(id);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('#advisor_personal_data_id').val('');
            }
        }
    });

    $('#advisor_institution_name').autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('institution/get_institutions_ajax')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                var list = rtn.data;
                var arr = [];
                arr = $.map(list, function(m){
                    return {
                        label: m.institution_name,
                        value: m.institution_name,
                        id: m.institution_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            $('#advisor_institution_id').val(id);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('#advisor_institution_id').val('');
            }
        }
    });

    $('#advisor_personal_data_name').autocomplete( "option", "appendTo", "#form_new_advisor" );
    $('#advisor_institution_name').autocomplete( "option", "appendTo", "#form_new_advisor" );

    $('button#submit_new_advisor').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 2000});
        var form = $('#form_new_advisor');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                $('#modal_add_advisor').modal('hide');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        })
    });
    
    advisor_select2($('#advisor_1_update'), $('input#advisor_1_institute_update'));
    advisor_select2($('#advisor_2_update'), $('input#advisor_2_institute_update'));
    advisor_select2($('#examiner_1_update'), $('input#examiner_1_institute_update'));
    advisor_select2($('#examiner_2_update'), $('input#examiner_2_institute_update'));
    advisor_select2($('#examiner_3_update'), $('input#examiner_3_institute_update'));
    advisor_select2($('#examiner_4_update'), $('input#examiner_4_institute_update'));
});

function show_view_file(action_fname) {
    var checked = submission_list_table.rows({selected: false});
    var count_checked = checked.count();

    if (count_checked <= 0) {
        toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
    }
    else {
        var data_check = checked.data();
        if (data_check.length > 0) {
            switch (action_fname) {
                case 'thesis_work':
                    if (condition) {
                        
                    }
                    break;
            
                default:
                    break;
            }
        }
        else {
            toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
        }
    }
}

function advisor_select2(el, elIns) {
    el.select2({
        minimumInputLength: 2,
        allowClear: true,
        placeholder: "Please select",
        theme: "bootstrap",
        ajax: {
            url: '<?=base_url()?>thesis/get_advisor_by_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function(result) {
                return {
                    results: $.map(result, function (item) {
                        return {
                            text: item.advisor_name,
                            id: item.advisor_id,
                            institution_id: item.institution_id,
                            institution_name: item.institution_name
                        }
                    })
                }
            }
        },
        language: {
            noResults: function(term) {
                return "No results found <button onclick='new_advisor()' class='btn btn-link'>+ Add Advisor</button>";
            }
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    el.on("change", function(e) { 
        var data_selected = el.select2('data');
        if (data_selected.length > 0) {
            elIns.val(data_selected[0].institution_name);
        }
        else {
            elIns.val('');
        }
    });
}

function new_advisor() {
    $('#advisor_personal_data_name').focus();
    $('.v_value').val('');
    $('#advisor_1_update').select2('close');
    $('#advisor_2_update').select2('close');
    $('#modal_add_advisor').modal('show');
}
</script>