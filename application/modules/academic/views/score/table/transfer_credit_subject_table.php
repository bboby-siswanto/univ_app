<div class="table-responsive">
    <table id="subject_transfer_credit" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Score</th>
                <th>Credit</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="6">Total SKS: 0</th>
            </tr>
        </tfoot>
    </table>
</div>
<script>
var table_subject_transfer = $('table#subject_transfer_credit').DataTable({
    ajax: {
        url: '<?=base_url()?>academic/transfer_credit/get_subject_student',
        type: 'POST',
        data: {
            student_id: '<?= $student_id;?>'
        }
    },
    columns: [
        {data: 'subject_code'},
        {data: 'subject_name'},
        {data: 'score_sum'},
        {data: 'curriculum_subject_credit', visible: false},
        {data: 'score_grade'},
        {
            data: 'score_id',
            render: function(data, type, row) {
                // var btn_transfer = '<button id="btn_transfer_credit_student" name="btn_transfer_credit_student" type="button" class="btn btn-info btn-sm" title="Transfer Subject"><i class="fas fa-long-arrow-alt-right"></i></button>';
                return '<button name="btn_remove_transfer_credit" type="button" id="btn_remove_transfer_credit" class="btn btn-danger btn-sm" title="Remove transfer credit" ><i class="fas fa-trash"></i></button>';
            }
        }
    ],
    footerCallback: function (row, data, start, end, display) {
        let api = this.api();
        let intVal = function (i) {
            return typeof i === 'string'
                ? i.replace(/[\$,]/g, '') * 1
                : typeof i === 'number'
                ? i
                : 0;
        };
 
        // Total over all pages
        total = api
            .column(3)
            .data()
            .reduce((a, b) => intVal(a) + intVal(b), 0);
 
        // Total over this page
        // pageTotal = api
        //     .column(2, { page: 'current' })
        //     .data()
        //     .reduce((a, b) => intVal(a) + intVal(b), 0);
 
        // Update footer
        api.column(3).footer().innerHTML =
            'Total ' + total + ' SKS';
    }
});
$(function(){
    $('table#subject_transfer_credit tbody').on('click', 'button[name="btn_remove_transfer_credit"]', function(e) {
        e.preventDefault();
        if (confirm('Are you sure remove this subject data?')) {
            var row_data = table_subject_transfer.row($(this).parents('tr')).data();
            $.blockUI();
            $.post('<?=base_url()?>academic/transfer_credit/remove_subject', {score_id: row_data.score_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    table_subject_transfer.ajax.reload();
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            })
        }
    });
})
</script>