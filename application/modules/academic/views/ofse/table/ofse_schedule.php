<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <!-- <button type="button" class="btn btn-secondary">Left</button> -->
            <a href="<?=base_url()?>academic/ofse/manage_ofse_schedule/<?=$ofse_period_id;?>" target="_blank" class="btn btn-success">New Schedule</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Schedule <?=$ofse_data->ofse_period_name;?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="list_schedule" class="table table-bordered table-hover">
                <thead>
                    <tr class="bg-dark">
                        <th>Day</th>
                        <th>Date</th>
                        <th>Room</th>
                        <th>Number Participant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_view_participant">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal_participant_schedule">Participant List </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <input type="hidden" id="view_ofse_period_id" name="ofse_period_id" value="<?=$ofse_period_id;?>">
                    <input type="hidden" id="view_exam_room" name="exam_room">
                    <input type="hidden" id="view_exam_date" name="exam_date">

                    <table id="table_pariticipant_list" class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-dark">
                                <th>Time</th>
                                <th>Student Name</th>
                                <th>Subject</th>
                                <th>Examiner</th>
                            <?php
                            if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                            ?>
                                <th>Action</th>
                            <?php
                            }
                            ?>
                            </tr>
                        </thead>
                    </table>
                </div>
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
<div class="modal" tabindex="-1" role="dialog" id="modal_update_examiner">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title student_name_for_examiner">Examiner</h5>
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
<script>
var list_schedule = $('table#list_schedule').DataTable({
    paging: false,
    ordering: false,
    // bInfo: false,
    ajax : {
        url : '<?= base_url()?>academic/ofse/get_ofse_schedule',
        type : 'POST',
        data : {
            ofse_period_id: '<?=$ofse_period_id;?>'
        }
    },
    columns: [
        {data: 'exam_day'},
        {data: 'exam_date_view'},
        {data: 'exam_room'},
        {data: 'exam_participant_count'},
        {
            data: 'ofse_exam_id',
            render: function(data, type, row) {
                // console.log(row.exam_date);
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                html += '<button type="button" class="btn btn-info" id="btn_view_participant">View Participant</button>';
                // html += '<button type="button" class="btn btn-success">Manage Schedule</button>';
                html += '<a href="<?=base_url()?>academic/ofse/manage_ofse_schedule/<?=$ofse_period_id;?>/' + row.exam_date + '/' + row.exam_room + '" target="_blank" class="btn btn-success">Manage Schedule</a>';
                html += '</div>';
                return html;
            }
        },
    ]
});

var table_pariticipant_list = $('table#table_pariticipant_list').DataTable({
    paging: false,
    bInfo: false,
    ordering: false,
    ajax: {
        url: '<?=base_url()?>academic/ofse/get_exam_room_participant',
        type: 'POST',
        data: function(d){
            d.ofse_period_id = $('#view_ofse_period_id').val(),
            d.exam_room = $('#view_exam_room').val(),
            d.exam_date = $('#view_exam_date').val()
        }
    },
    columns: [
        {
            data: 'exam_time_start',
            render: function(data, type, row) {
                return data + '-' + row.exam_time_end;
            }
        },
        {data: 'personal_data_name'},
        {data: 'subject_name'},
        {
            data: 'list_examiner',
            render: function(data, type, row) {
                return data + '<span class="d-none">' + row.score_id + '</span>';
            }
        },
    <?php
    if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
    ?>
        {
            data: 'score_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                html += '<button type="button" class="btn btn-success" id="update_examiner">Update Examiner</button>';
                html += '<button type="button" class="btn btn-info" id="examinerlist">Examiner List</button>';
                html += '</div>';
                return html;
            }
        },
    <?php
    }
    ?>
    ]
});

$(function() {
    $('table#list_schedule tbody').on('click', 'button#btn_view_participant', function(e) {
        e.preventDefault();

        var row_data = list_schedule.row($(this).parents('tr')).data();
        $('input#view_exam_room').val(row_data.exam_room);
        $('input#view_exam_date').val(row_data.exam_date);
        
        // $('input#view_exam_date').promise().done(function() {
        //     table_pariticipant_list.ajax.reload();
        //     $('#modal_view_participant').modal('show');
        // });
        $('.modal_participant_schedule').text('Participant Lists ' + row_data.exam_date + '/' + row_data.exam_room);
        table_pariticipant_list.ajax.reload();
        $('#modal_view_participant').modal('show');
    });
    
    $('table#table_pariticipant_list tbody').on('click', 'button#update_examiner', function(e) {
        e.preventDefault();

        var row_data = table_pariticipant_list.row($(this).parents('tr')).data();
        // console.log(row_data);
        if (row_data.examiner_data) {
            $.each(row_data.examiner_data, function(i, val) {
                let examinertype = val.examiner_type;
                let examinernumber = examinertype.substring((examinertype.length - 1),examinertype.length);

                $('#examiner_' + examinernumber + '_update').val(val.personal_data_name);
                $('#examiner_' + examinernumber + '_id_update').val(val.advisor_id);
                $('#examiner_' + examinernumber + '_id_institute_update').val(val.institution_id);
                $('#examiner_' + examinernumber + '_institute_update').val(val.institution_name);
            });
        }

        $('.student_name_for_examiner').text('Examiner for ' + row_data.personal_data_name);
        $('#score_id').val(row_data.score_id);
        $('#modal_view_participant').modal('hide');
        $('#modal_update_examiner').modal('show');
        table_pariticipant_list.ajax.reload();
    });
    
    $('table#table_pariticipant_list tbody').on('click', 'button#examinerlist', function(e) {
        e.preventDefault();

        var row_data = table_pariticipant_list.row($(this).parents('tr')).data();
        // // console.log(row_data);
        // if (row_data.examiner_data) {
        //     $.each(row_data.examiner_data, function(i, val) {
        //         let examinertype = val.examiner_type;
        //         let examinernumber = examinertype.substring((examinertype.length - 1),examinertype.length);

        //         $('#examiner_' + examinernumber + '_update').val(val.personal_data_name);
        //         $('#examiner_' + examinernumber + '_id_update').val(val.advisor_id);
        //         $('#examiner_' + examinernumber + '_id_institute_update').val(val.institution_id);
        //         $('#examiner_' + examinernumber + '_institute_update').val(val.institution_name);
        //     });
        // }

        // $('.student_name_for_examiner').text('Examiner for ' + row_data.personal_data_name);
        // $('#score_id').val(row_data.score_id);

        $('input#examiner_score_id').val(row_data.score_id);
        if ($.fn.DataTable.isDataTable('#table_ofse_list_examiner')){
            table_ofse_list_examiner.ajax.reload(null, false);
        }

        $('#modal_view_participant').modal('hide');
        $('#modal_examiner_list').modal('show');
    });
})
</script>