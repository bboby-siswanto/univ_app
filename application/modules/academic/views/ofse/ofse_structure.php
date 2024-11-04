<div class="card">
    <div class="card-header">OFSE Structure <span><?=$ofse_data->ofse_period_name;?></span></div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_ofse_member" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Study Program</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
<script>
var table_lists_ofse_stucture = $('table#table_ofse_member').DataTable({
    paging: false,
    // bInfo: false,
    ajax : {
        url : '<?= base_url()?>academic/ofse/get_ofse_structure',
        type : 'POST',
        data : {
            ofse_period_id: '<?=$ofse_data->ofse_period_id;?>'
        }
    },
    columns: [
        {data: 'student_number'},
        {
            data: 'personal_data_name',
            render: function(data, type, row) {
                if ('<?=$this->session->userdata('user')?>' != '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
                    return '<a href="javascript:void(0)" id="view_question" title="View Question">' + data + '</a>';
                }
                else {
                    return data;
                }
            }
        },
        {
            data: 'study_program_abbreviation',
            render: function(data, type, row) {
                if (data == 'COS') {
                    data = 'CSE';
                }

                return data;
            }
        },
        {data: 'ofse_subject_code'},
        {data: 'subject_name'},
        {
            data: 'score_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm">';
                if ('<?=$this->session->userdata('user')?>' != '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
                    html += '<button type="button" class="btn btn-info btn-sm" id="btn_view_question" data-toggle="tooltip" data-placement="bottom" title="Open Question Varian"><i class="fas fa-flag"></i> Open Question Varian</button>';
                }
                html += '<a href="<?=base_url()?>academic/ofse/result_score/' + data + '" class="btn btn-info btn-sm" id="btn_view_score" data-toggle="tooltip" data-placement="bottom" title="View Score Result"><i class="fas fa-poll"></i> View Score Result</a>';
                html += '</div>';
                return html;
                // 
            }
        },
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
                var q_number = row.ofse_question_sequence;
                html += '<a class="btn btn-success btn-sm" target="_blank" href="<?=base_url()?>student/ofse/question/' + row.ofse_period_id + '/' + row.subject_question_id + '/' + row.ofse_subject_code + '" title="Open Question">Open Question</a>';
                html += '<a class="btn btn-warning btn-sm" href="<?=base_url()?>academic/ofse/form_evaluation/' + $('input#q_ofse_score_id').val() + '/' + row.subject_question_id + '/' + row.ofse_subject_code + '" title="Open Evaluation Sheet">Open Evaluation Sheet</a>';
                // html += '<button class="btn btn-warning btn-sm" id="open_evaluation" type="button" title="Open Evaluation Sheet">Evaluation Sheet</button>';
                html += '</div>';
                return html;
            }
        },
        
    ]
});

$(function() {
    $('table#table_ofse_member tbody').on('click', 'a[id="view_question"], button#btn_view_question', function(e) {
        e.preventDefault();
        
        var data = table_lists_ofse_stucture.row($(this).parents('tr')).data();

        $('input#q_ofse_subject_code').val(data.ofse_subject_code);
        $('input#q_ofse_score_id').val(data.score_id);
        $('#q_subject_name').text(data.subject_name + ' / ' + data.ofse_subject_code);
        question_subject_list.ajax.reload();
        $('div#modal_question_list').modal('show');
    });
});
</script>