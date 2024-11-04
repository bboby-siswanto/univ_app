<div class="card">
    <div class="card-header">
        OFSE Subject List
    </div>
    <div class="card-body">
        <table id="ofse_subject_list" class="table table-bordered table-hover">
            <thead class="bg-dark">
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Subject Type</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_subject_question">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question List <span id="q_subject_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="subject_question_modal">
                <input type="hidden" id="q_ofse_period_id" name="q_ofse_period_id" value="<?=$ofse_data->ofse_period_id;?>">
                <input type="hidden" id="q_ofse_subject_code" name="q_ofse_subject_code">
                <input type="hidden" id="q_ofse_score_id" name="q_ofse_score_id">
                <table id="q_subject_list" class="table table-bordered table-hover">
                    <thead class="bg-dark">
                        <tr>
                            <th>No</th>
                            <th>Question</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
var ofse_subject_list = $('table#ofse_subject_list').DataTable({
    ordering: false,
    ajax : {
        url : '<?= base_url()?>academic/ofse/student_krs/<?=$ofse_data->ofse_period_id;?>/<?=$ofse_data->student_id;?>',
        type : 'POST'
    },
    columns: [
        {
            data: 'ofse_subject_code',
            render: function(data, type, row) {
                var html = '<a href="javascript:void(0)" id="view_question" title="View Question">' + data + '</a>';
                html += '<input type="hidden" name="key_score" value="' + row.score_id + '">';
                return html;
            }
        },
        {data: 'subject_name'},
        {data: 'ofse_status'},
        {data: 'ofse_schedule'},
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
            data: 'subject_question_id',
            render: function(data, type, row) {
                if ((row.has_pick == 'false') || (row.is_pick_student)) {
                // if (row.has_pick == 'false') {
                    var html = '<div class="btn-group btn-group-sm">';
                    html += '<a class="btn btn-success btn-sm" href="<?=base_url()?>student/ofse/question/' + row.ofse_period_id + '/' + data + '/' + row.ofse_subject_code + '" target="blank" title="Open Question">Pick Question</a>';
                    html += '</div>';
                    return html;
                }
                else {
                    return '';
                }
            }
        },
        
    ]
});

var dtnow = new Date($.now());
var current = dtnow.toLocaleString("en-US", {timeZone: "Asia/Jakarta"});
var dtnow = new Date(current);
$(function() {
    $('table#ofse_subject_list tbody').on('click', 'a#view_question', function(e) {
        e.preventDefault();

        var data = ofse_subject_list.row($(this).parents('tr')).data();
        if (data.ofse_exam_date && data.ofse_exam_time_start && data.ofse_exam_time_end) {
            var dtexam_start = data.ofse_exam_date + ' ' + data.ofse_exam_time_start;
            var dtexam_end = data.ofse_exam_date + ' ' + data.ofse_exam_time_end;
            // new Date('2011-04-12'.replace(/-/g, "/"))
            let dtdeff = new Date(Date.parse(dtexam_start));
            let dtdeff_end = Date.parse(dtexam_end);

            var diff = new Date(dtdeff - dtnow);
            var diff_end = new Date(dtdeff_end - dtnow);
            var minute = diff/1000/60;
            var minute_end = diff_end/1000/60;

            // minute_end = (isNaN(minute_end)) ? 0 : minute_end;
            // var days = diff_end/1000/60/60/24;

            console.log(current);
            // 10 menit sebelum ujian dimulai
            if ((minute <= 10) && (minute_end >= 0)) {
                $('input#q_ofse_subject_code').val(data.ofse_subject_code);
                $('input#q_ofse_score_id').val(data.score_id);
                $('#q_subject_name').text(data.subject_name + ' / ' + data.ofse_subject_code);

                question_subject_list.ajax.reload();
                $('div#modal_subject_question').modal('show');
            }
            else {
                toastr.warning('Questions can be opened when the exam starts according to schedule! ');
                console.log(dtdeff);
            }
        }
        else {
            toastr.warning('Schedule not available!');
        }
    });
    
    // $('table#q_subject_list tbody').on('click', 'a#q_view_question', function(e) {
    //     e.preventDefault();

    //     var data = ofse_subject_list.row($(this).parents('tr')).data();

    //     $('input#q_ofse_subject_code').val(data.ofse_subject_code);
    //     $('input#q_ofse_score_id').val(data.score_id);
    //     $('#q_subject_name').text(data.subject_name + ' / ' + data.ofse_subject_code);

    //     question_subject_list.ajax.reload();
    //     $('div#modal_subject_question').modal('show');
    // });
});

function convertTZ(date, tzString) {
    return new Date((typeof date === "string" ? new Date(date) : date).toLocaleString("en-US", {timeZone: tzString}));   
}
</script>