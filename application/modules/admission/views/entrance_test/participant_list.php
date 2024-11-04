<?=modules::run('admission/entrance_test/form_filter');?>
<div class="card">
    <div class="card-header">
        Participant List
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" id="button_retrieve_participant" title="retrieve data from students with register and candidate status">
                <i class="fas fa-sliders-h"></i> Retrieve From Student
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="row">
                <div class="col">
                    <button id="btn_refresh_datatable" class="btn btn-secondary btn-sm" type="button"><i class="fas fa-sync"></i></button>
                </div>
            </div>
            <table class="table table-bordered table-striped" id="table_participant">
                <thead class="bg-dark">
                    <tr>
                        <th>No</th>
                        <th>Participant Name</th>
                        <th>Email</th>
                        <th>Token</th>
                        <th>Starting Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="extra_time_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Extra Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_extra_time" onsubmit="return false">
                    <input type="hidden" name="exam_candidate_id" id="exam_candidate_id">
                    <div class="form-group">
                        <label for="extra_minutes">Add extra minutes</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="extra_minutes" id="extra_minutes" aria-label="" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2"> minutes</span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label>Add to section</label>
                        <select name="option_time" id="option_time" class="form-control">
                    <?php
                        // if ($section_list) {
                        //     foreach ($section_list as $section) {
                    ?>
                            <option value="<?php // echo $section->exam_section_id;?>"><?php // echo $section->exam_section_name;?></option>
                    <?php
                        //     }
                        // }
                    ?>
                            <option value="all">Add to All</option>
                        </select>
                    </div>
                    <small class="text-danger">if you select add to all, each section will be added according to input time</small> -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_extra_time">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var participant_table = $('#table_participant').DataTable({
        ajax: {
            url: '<?=base_url()?>admission/entrance_test/participant_list',
            type: 'POST',
            data: function(d){
                d.academic_year_id = $('select#academic_year_id_filter').val(),
                d.participant_type = $('select#select_participant').val(),
                d.event_key = $('select#event_key').val()
            }
        },
        columns: [
            {
                data: 'exam_candidate_id',
                orderable: false,
                render: function ( data, type, row, meta ) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'personal_data_name' ,
                render: function(data, type, row) {
                    html = '<a href="<?=base_url()?>admission/entrance_test/participant_result/' + row.exam_candidate_id + '" target="_blank">' + data + '</a>';
                    return html;
                }
            },
            { data: 'personal_data_email' },
            { 
                data: 'token' ,
                render: function(data, type, row) {
                    // var token = data.substring(1, ((data.length) - 9));
                    // token += 'xxxx-xxxx';
                    // return token;
                    return data;
                }
            },
            { data: 'start_time' },
            { data: 'end_time' },
            { 
                data: 'candidate_exam_status' 
            },
            {
                data: 'exam_candidate_id' ,
                orderable: false,
                render: function(data, type, row) {
                    var status = row.candidate_exam_status;
                    var btn_reset_token = '<button type="button" id="reset_token" title="Reset Token" class="btn btn-sm btn-info"><i class="fas fa-random"></i></button>';
                    var btn_prop_cancel_token = '<button type="button" id="cancel_token" title="Cancel Token" class="btn btn-sm btn-success"><i class="fas fa-times-circle"></i></button>';
                    var btn_prop_active_token = '<button type="button" id="activated_token" title="Activate Token" class="btn btn-sm btn-danger"><i class="fas fa-check-circle"></i></button>';
                    var btn_view_token = '<button type="button" id="view_token" title="View Token" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>';
                    var btn_add_time = '<button type="button" id="add_extra_time" title="Add Extra Time" class="btn btn-sm btn-info"><i class="fas fa-stopwatch"></i></button>';
                    var btn_send_token = '<button type="button" id="send_token" title="Send Token to Participant email" class="btn btn-sm btn-info"><i class="fas fa-envelope"></i></button>';
                    var btn_reset_data = '<button type="button" id="reset_data" title="Reset data" class="btn btn-sm btn-danger"><i class="fas fa-exclamation-circle"></i></button>';
                    var btn_set_status = '<div class="btn-group" role="group">'
                        btn_set_status += '<button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Set Status Exam"><i class="fas fa-user"></i></button>';
                        btn_set_status += '<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">';
                        btn_set_status += '<button type="button" class="dropdown-item" id="set_to_pending">to PENDING</a>';
                        btn_set_status += '</div></div>';
                    html = '<div class="btn-group" role="group" aria-label="">';
                    html += btn_reset_token;
                    if (row.candidate_exam_status == 'CANCEL') {
                        html += btn_prop_active_token;
                    }else if(row.candidate_exam_status == 'PENDING'){
                        html += btn_prop_cancel_token;
                    }else if(row.candidate_exam_status == 'PROGRESS'){
                        if (('<?=$this->session->userdata("name");?>' == 'BUDI SISWANTO') && ('<?=$this->session->userdata("name");?>' == 'HARMANDO TAUFIK GEMILANG')) {
                            // html += btn_add_time;
                        }
                    }
                    html += btn_reset_data;
                    html += btn_set_status;
                    // html += btn_send_token; 
                    // html += btn_view_token;
                    html += '</div>';
                    return html;
                }
            }
        ]
    });
    $('button#filter_participant').on('click', function(e) {
        e.preventDefault();
        participant_table.ajax.reload(null, true);
    });

    $('button#btn_refresh_datatable').on('click', function(e) {
        e.preventDefault();
        participant_table.ajax.reload(null, true);
    });

    $('button#button_retrieve_participant').on('click', function(e) {
        e.preventDefault();
        $.blockUI();
        $.post('<?=base_url()?>admission/entrance_test/submit_candidate_exam', {}, function(result) {
            $.unblockUI();
            participant_table.ajax.reload();
            toastr.success('Finish!', 'Success');
        }, 'json').fail(function(a, b, c) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error!');
        });
    });

    $('button#submit_extra_time').on('click', function(e) {
        e.preventDefault();

        $.blockUI();
        var data = $('#form_extra_time').serialize();
        $.post('<?=base_url()?>admission/entrance_test/extra_time', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#extra_time_modal').modal('hide');
                toastr.success('Success add extra time', 'Success');
                participant_table.ajax.reload(null, false);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error!');
        });
    });

    $('table#table_participant tbody').on('click', 'button#reset_data', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();
        if(confirm('Are you sure to reset all data ' + data.personal_data_name + ' ?')) {
            if (confirm('Are you sure again ?')) {
                $.post('<?=base_url()?>admission/entrance_test/reset_data', {exam_candidate_id: data.exam_candidate_id}, function(result) {
                    if (result.code == 0) {
                        participant_table.ajax.reload(null, true);
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    toastr.error('Error processing data!', 'Error!');
                });
            }
        }
        
        // console.log(data.exam_candidate_id);
    });
    
    $('table#table_participant tbody').on('click', 'button#set_to_pending', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();
        if(confirm('Are you sure to set status ' + data.personal_data_name + ' to PENDING?')) {
            $.post('<?=base_url()?>admission/entrance_test/set_pending_status', {exam_candidate_id: data.exam_candidate_id}, function(result) {
                if (result.code == 0) {
                    participant_table.ajax.reload(null, true);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Error processing data!', 'Error!');
            });
        }
        
        // console.log(data.exam_candidate_id);
    });

    $('table#table_participant tbody').on('click', 'button#send_token', function(e) {
        e.preventDefault();

        if(confirm('send token via email to participant ?')) {
            var data = participant_table.row($(this).parents('tr')).data();

            $.blockUI();
            $.post('<?=base_url()?>admission/entrance_test/send_token', {exam_candidate_id : data.exam_candidate_id}, function(result){
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Message send!', 'Success!');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        }
    });

    $('table#table_participant tbody').on('click', 'button#add_extra_time', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();

        $('input#exam_candidate_id').val(data.exam_candidate_id);
        $('#extra_time_modal').modal('show');
        $('#extra_minutes').focus();
    });

    $('table#table_participant tbody').on('click', 'button#view_token', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();
        toastr.success(data.token);
    });

    $('table#table_participant tbody').on('click', 'button#activated_token', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();
        prop_token_status('PENDING', data.exam_candidate_id);
    });

    $('table#table_participant tbody').on('click', 'button#cancel_token', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();
        prop_token_status('CANCEL', data.exam_candidate_id);
    });

    $('table#table_participant tbody').on('click', 'button#reset_token', function(e) {
        e.preventDefault();

        var data = participant_table.row($(this).parents('tr')).data();
        if (confirm('Are you sure reset token this participant ?')) {
            let exam_candidate_id = data.exam_candidate_id;
            $.blockUI();
            $.post('<?=base_url()?>admission/entrance_test/reset_token', {exam_candidate_id: exam_candidate_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Token has been reset!', 'Success!');
                    participant_table.ajax.reload(null, true);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        }
    });

    function prop_token_status(status, candidate_exam_id) {
        $.blockUI();
        $.post('<?=base_url()?>admission/entrance_test/prop_status_token', {status: status, exam_candidate_id: candidate_exam_id}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Status has been changed!', 'Success!');
                participant_table.ajax.reload(null, true);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error');
        });
    }
</script>