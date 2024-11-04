<div class="card">
<div class="card-header">
    Responden List
</div>
<div class="card-body">
    <div class="table-responsive">
        <table id="table_respondentlist_result" class="table table-hover table-border">
            <thead class="bg-dark">
                <tr>
                    <th>Study Program</th>
                    <th>Total Respondent</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th>Total Responden</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</div>
<script>
var table_respondentlist_result = $('#table_respondentlist_result').DataTable({
    ordering: false,
    paging: false,
    searching: false,
    info: false,
    ajax: {
        url: '<?=base_url()?>validation_requirement/university_assessment/get_responden_result',
        type: 'POST'
    },
    columns: [
        {data: 'study_program_name'},
        {data: 'total_responden'}
    ],
    footerCallback: function (row, data, start, end, display) {
        var api = this.api();
        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };
        // Total over this page
        pageTotal = api
            .column(1, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        // Update footer
        $(api.column(1).footer()).html(pageTotal + ' Students');
    }
});
</script>