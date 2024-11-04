<div class="card">
    <div class="card-header">
        <?=$student_data->personal_data_name;?> - Subject List <?=$ofse_data->ofse_period_name;?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="ofse_student_krs" class="table table-bordered">
                <thead class="bg-dark">
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>OFSE Status</th>
                        <th>Examiner</th>
                        <th>OFSE Schedule</th>
                        <th>Final Score</th>
                        <th>Grade</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_input_score_ofse">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('academic/ofse/form_input_score');?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_ofse_score">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_input_examiner">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Examiner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= modules::run('academic/ofse/form_examiner');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_examiner_list">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Examiner List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= modules::run('academic/ofse/list_examiner');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_question_list">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Varian Question <span id="q_subject_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="q_ofse_period_id" name="q_ofse_period_id" value="<?=$ofse_data->ofse_period_id;?>">
                <input type="hidden" id="q_ofse_subject_code" name="q_ofse_subject_code">
                <input type="hidden" id="q_ofse_score_id" name="q_ofse_score_id">
                <table id="q_subject_list" class="table table-bordered table-hover">
                    <thead class="bg-dark">
                        <tr>
                            <th>No</th>
                            <th>Varian</th>
                            <th>Has Pick</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="ofse_subject_schedule">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">OFSE Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_schedule" onsubmit="return false" url="<?=base_url()?>academic/ofse/submit_shcedule">
                    <input type="hidden" name="sc_score_id" id="sc_score_id">
                    <div class="row">
                        <div class="col-sm-6">
                            <label id="schedule_student">Student Name</label><br>
                            <label id="schedule_subject">Subject</label><br>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Examiner:</label><br>
                                <label id="schedule_examiner">examiner</label><br>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="sc_room">Room</label>
                                <input type="text" name="sc_room" id="sc_room" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="sc_date">Date</label>
                                <input type="date" name="sc_date" id="sc_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="sc_time">Time</label>
                                <select name="sc_time" id="sc_time" class="form-control">
                        <?php
                        for ($i=8; $i <=21 ; $i++) { 
                            $text_start = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':00' : $i.':00';
                            $text_end = (strlen($i + 1) == 1) ? str_pad(($i + 1), 2, "0", STR_PAD_LEFT).':00' : ($i + 1).':00';
                        ?>
                                    <option value="<?=$i;?>"><?= $text_start.' - '.$text_end ?></option>
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
                <button type="button" class="btn btn-primary" id="submit_schedule">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var ofse_student_krs = $('table#ofse_student_krs').DataTable({
    paging: false,
    bInfo: false,
    ajax : {
        url : '<?= base_url()?>academic/ofse/student_krs/<?=$ofse_data->ofse_period_id;?>/<?=$student_data->student_id;?>',
        type : 'POST'
    },
    columns: [
        {data: 'ofse_subject_code'},
        {data: 'subject_name'},
        {data: 'ofse_status'},
        {
            data: 'ofse_examiner_name',
        },
        {data: 'ofse_schedule'},
        {data: 'score_sum'},
        {data: 'score_grade'},
        {
            data: 'score_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm">';
                html += '<button class="btn btn-info btn-sm" id="input_score" type="button" title="Input Score"><i class="fas fa-book"></i></button>';
                html += '<button class="btn btn-info btn-sm" id="input_examiner" type="button" title="Input Examiner"><i class="fas fa-user-plus"></i></button>';
                html += '<button class="btn btn-info btn-sm" id="examiner_list" type="button" title="Examiner List"><i class="fas fa-users-cog"></i></button>';
                html += '<button class="btn btn-info btn-sm" id="question_list" type="button" title="View Question"><i class="fas fa-chalkboard"></i></button>';
                html += '<button class="btn btn-info btn-sm" id="input_schedule" type="button" title="Input Schedule"><i class="fas fa-calendar"></i></button>';
                html += '<a href="<?=base_url()?>academic/ofse/result_score/' + data + '" class="btn btn-info btn-sm" id="btn_view_score" data-toggle="tooltip" data-placement="bottom" title="View Score Result"><i class="fas fa-poll"></i></a>';
                html += '</div>';
                return html;
            }
        }
    ]
});

var question_subject_list = $('table#q_subject_list').DataTable({
    paging: false,
    info: false,
    order: false,
    ajax: {
        url: '<?= base_url()?>academic/ofse/get_ofse_subject_question',
        type: 'POST',
        data: function(d) {
            d.ofse_period_id = $('input#q_ofse_period_id').val();
            d.ofse_subject_code = $('input#q_ofse_subject_code').val();
            d.ofse_score_id = $('input#q_ofse_score_id').val();
        },
    },
    columns: [
        {data: 'ofse_question_sequence'},
        {
            data: 'subject_name',
            render: function(data, type, row) {
                return data + ' No. ' + row.ofse_question_sequence;
            }
        },
        {
            data: 'has_pick',
            render: function(data, type, row) {
                if (data == 'true') {
                    return 'YES';
                }
                else {
                    return 'NO';
                }
            }
        },
        {
            data: 'subject_question_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm">';
                // html += '<a class="btn btn-success btn-sm" target="_blank" href="<?=base_url()?>academic/ofse/view_question/' + row.ofse_period_id + '/' + row.ofse_subject_code + '" title="Open Question">Open Question</a>';
                html += '<a class="btn btn-success btn-sm" target="_blank" href="<?=base_url()?>student/ofse/question/' + row.ofse_period_id + '/' + row.subject_question_id + '/' + row.ofse_subject_code + '" title="Open Question">Open Question</a>';
                html += '<a class="btn btn-warning btn-sm" href="<?=base_url()?>academic/ofse/form_evaluation/' + $('input#q_ofse_score_id').val() + '/' + row.ofse_subject_code + '" title="Open Evaluation Sheet">Open Evaluation Sheet</a>';
                // html += '<button class="btn btn-warning btn-sm" id="open_evaluation" type="button" title="Open Evaluation Sheet">Evaluation Sheet</button>';
                html += '</div>';
                return html;
            }
        },
        
    ]
});
$(function() {
    $('#modal_input_examiner').on('hidden.bs.modal', function (e) {
        $('input, textarea, select').val('');
    });

    $('table#ofse_student_krs tbody').on('click', 'button#input_examiner', function(e) {
        e.preventDefault();

        var data = ofse_student_krs.row($(this).parents('tr')).data();
        $('input#score_id').val(data.score_id);
        $('#modal_input_examiner').modal('show');
    });
    
    $('table#ofse_student_krs tbody').on('click', 'button#examiner_list', function(e) {
        e.preventDefault();

        var data = ofse_student_krs.row($(this).parents('tr')).data();
        $('input#examiner_score_id').val(data.score_id);
        table_ofse_list_examiner.ajax.reload();
        $('#modal_examiner_list').modal('show');
    });

    $('table#ofse_student_krs tbody').on('click', 'button[id="input_score"]', function(e) {
        e.preventDefault();
        
        var data = ofse_student_krs.row($(this).parents('tr')).data();

        $('input#score_id').val(data.score_id);
        $('div#modal_input_score_ofse').modal('show');
    });

    $('table#ofse_student_krs tbody').on('click', 'button[id="question_list"]', function(e) {
        e.preventDefault();
        
        var data = ofse_student_krs.row($(this).parents('tr')).data();

        $('input#q_ofse_subject_code').val(data.ofse_subject_code);
        $('input#q_ofse_score_id').val(data.score_id);
        $('#q_subject_name').text(data.subject_name + ' / ' + data.ofse_subject_code);
        question_subject_list.ajax.reload();
        $('div#modal_question_list').modal('show');
    });

    $('table#ofse_student_krs tbody').on('click', 'button[id="input_schedule"]', function(e) {
        e.preventDefault();
        
        var data = ofse_student_krs.row($(this).parents('tr')).data();

        $('input#sc_score_id').val(data.score_id);
        $('#schedule_student').text(data.personal_data_name);
        $('#schedule_subject').text(data.subject_name + ' / ' + data.ofse_subject_code);
        $('div#ofse_subject_schedule').modal('show');
    });
    
    $('button#submit_ofse_score').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 2000});

        let form = $('form#ofse_input_score');
        var data = form.serialize();

        $.post('<?=base_url()?>academic/ofse/save_ofse_score', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr['success']('Success save data', 'Success');
                $('div#modal_input_score_ofse').modal('hide');
                ofse_student_krs.ajax.reload(null, false);
            }else{
                toastr['warning'](result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr['error']('Error saving data', 'Error');
        })
    });

    $('button#submit_schedule').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 2000});

        let form = $('form#form_schedule');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr['success']('Success save data', 'Success');
                ofse_student_krs.ajax.reload();
                $('div#ofse_subject_schedule').modal('hide');
            }else{
                toastr['warning'](result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr['error']('Error saving data', 'Error');
        })
    });
});
</script>