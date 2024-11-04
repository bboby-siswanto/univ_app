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
                    <th>Total Varian Question</th>
                    <th>Total Student</th>
                    <th>Action</th>
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
                <h5 class="modal-title">Question File <span id="subject_name_modal"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="subject_question_modal">
                <?=modules::run('academic/ofse/ofse_subject_question');?>
            </div>
        </div>
    </div>
</div>
<script>
var ofse_subject_list = $('table#ofse_subject_list').DataTable({
    ajax : {
        url : '<?= base_url()?>academic/ofse/get_list_ofse_subject',
        type : 'POST',
        data: function(d) {
            d.ofse_period_id = '<?=$ofse_period_id;?>';
        }
    },
    columns: [
        {data: 'subject_code'},
        {data: 'subject_name'},
        {data: 'subject_type'},
        {
            data: 'count_question',
            render: function(data, type, row) {
                var html = data;
                var min_question = parseInt(row.count_student) + 1;
                var current_question = parseInt(data);
                var diff = current_question - min_question;
                if (diff < 0) {
                    html += ' &nbsp;<span class="badge badge-danger">' + diff + ' varian question</span>';
                }
                return html;
            }
        },
        {data: 'count_student'},
        {
            data: 'subject_question_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm">';
                html += '<button class="btn btn-info btn-sm" id="btn_question_subject" type="button" title="Question File"><i class="fas fa-book"></i></button>';
                // html += '<button class="btn btn-info btn-sm" id="input_examiner" type="button" title="Input Examiner"><i class="fas fa-user-plus"></i></button>';
                // html += '<button class="btn btn-info btn-sm" id="examiner_list" type="button" title="Examiner List"><i class="fas fa-users-cog"></i></button>';
                html += '</div>';
                return html;
            }
        }
    ]
});

$(function() {
    $('table#ofse_subject_list tbody').on('click', 'button#btn_question_subject', function(e) {
        e.preventDefault();

        var data = ofse_subject_list.row($(this).parents('tr')).data();
        var params = {
            ofse_subject_code: data.subject_code,
            ofse_period_id: data.ofse_period_id,
            count_student: data.count_student,
            subject_id: data.subject_id
        }
        $.blockUI();
        
        $.post('<?=base_url()?>academic/ofse/ofse_subject_question', params, function(result) {
            $.unblockUI();
            if (result.html.length > 0) {
                $('#subject_name_modal').text(data.subject_name);
                $('#subject_question_modal').html(result.html);
                $('#modal_subject_question').modal('show');
            }else{
                toastr['warning']('failed retrieve form', 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr['error']('Error processing data', 'Error');
        })
    });
})
</script>