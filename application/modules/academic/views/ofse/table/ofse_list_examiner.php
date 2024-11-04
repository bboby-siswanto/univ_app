<input type="hidden" name="examiner_score_id" id="examiner_score_id">
<table id="table_ofse_list_examiner" class="table table-bordered table-hover">
    <thead class="bg-dark">
        <tr>
            <th>Examiner Name</th>
            <th>Institute</th>
            <th>Examiner</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script>
var table_ofse_list_examiner = $('table#table_ofse_list_examiner').DataTable({
    ordering: false,
    paging: false,
    info: false,
    searching: false,
    ajax: {
        url: '<?= base_url()?>academic/ofse/get_examiner_list',
        type: 'POST',
        data: function(d) {
            d.score_id = $('input#examiner_score_id').val();
        },
    },
    columns: [
        {data: 'examiner_name'},
        {data: 'institution_name'},
        {data: 'examiner_type'},
        {
            data: 'student_examiner_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm">';
                html += '<button class="btn btn-danger btn-sm" id="remove_examiner" type="button" title="Drop Examiner"><i class="fas fa-trash"></i></button>';
                if (row.examiner_lock_evaluation == 'true') {
                    html += '<button class="btn btn-success btn-sm" id="unlock_evaluation" type="button" title="Unlock Evaluation"><i class="fas fa-check"></i></button>';
                }
                html += '</div>';
                return html;
            }
        },
        
    ]
});

$(function() {
    $('table#table_ofse_list_examiner tbody').on('click', 'button#remove_examiner', function(e) {
        e.preventDefault();

        if (confirm('Are you sure?')) {
            var data = table_ofse_list_examiner.row($(this).parents('tr')).data();
        
            $.blockUI();
            $.post('<?=base_url()?>academic/ofse/remove_examiner', {student_examiner_id: data.student_examiner_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!', 'Success');
                    table_ofse_list_examiner.ajax.reload();
                }
                else {
                    toastr.warning(result.message);
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        }
    });

    $('table#table_ofse_list_examiner tbody').on('click', 'button#unlock_evaluation', function(e) {
        e.preventDefault();

        if (confirm('Are you sure to unlock ?')) {
            var data = table_ofse_list_examiner.row($(this).parents('tr')).data();
        
            $.blockUI();
            $.post('<?=base_url()?>academic/ofse/unlock_evaluation', {student_examiner_id: data.student_examiner_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!', 'Success');
                    table_ofse_list_examiner.ajax.reload();
                }
                else {
                    toastr.warning(result.message);
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        }
    });
})
</script>