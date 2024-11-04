<table id="table_all_score" class="table table-bordered table-hover table-striped">
    <thead class="bg-dark">
        <tr>
            <th>No</th>
            <th>Subject Name</th>
            <th>Subject Code</th>
            <th>Quiz</th>
            <th>Final Exam</th>
            <th>Repetition Exam</th>
            <th>Final Score</th>
            <th>Grade</th>
            <th>Grade Point</th>
            <th>Credit / SKS</th>
            <th>Merit</th>
            <th>Semester</th>
            <th>Display Score</th>
        </tr>
    </thead>
</table>
<script>
    var view_score_all_student = function(data_filter) {
        if($.fn.DataTable.isDataTable('table#table_all_score')){
            score_datatable.destroy();
        }

        var score_datatable = $('table#table_all_core').DataTable({
            // ordering: false,
            // searching: false,
            info: false,
            paging: false,
            processing: true,
            ajax: {
                url: '<?= base_url()?>academic/score/filter_score_student',
                type: 'POST',
                data: data_filter
            },
            columns: [
                {data: 'score_id'},
                {data: 'subject_name'},
                {data: 'subject_code'},
                {data: 'score_quiz'},
                {data: 'score_final_exam'},
                {data: 'score_repetition_exam'},
                {data: 'score_sum'},
                {data: 'score_grade'},
                {data: 'score_grade_point'},
                {data: 'curriculum_subject_credit'},
                {data: 'score_merit'},
                {data: 'semester_number'},
                {data: 'score_display'}
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function ( data, type, row, meta ) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }
            ],
            createdRow: function(row, data, index) {
                if (data.score_grade == 'F') {
                    $(row).addClass("table-danger");
                }else if(data.score_grade == 'D') {
                    $(row).addClass("table-warning");
                }
                // console.log(data);
            }
            // footerCallback: function(row, data, start, end, display) {
            //     var api = this.api(), data;

            //     var intVal = function ( i ) {
            //         return typeof i === 'string' ?
            //             i.replace(/[\$,]/g, '')*1 :
            //             typeof i === 'number' ?
            //                 i : 0;
            //     };

            //     total_sks = api.column(16, {page:'current'}).data().reduce(function(a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0);

            //     total_merit = api.column(17, {page:'current'}).data().reduce(function(a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0);

            //     $(api.column(16).footer()).html(total_sks);
            //     $(api.column(17).footer()).html(total_merit.toFixed(2));
            //     $('#gpa_result').html((total_merit/total_sks).toFixed(2));
            // }
        });
    }

    $(function() {
        view_score_all_student({personal_data_id: '<?= $personal_data_id;?>', semester_id: '<?= $semester_id?>'});
    });
</script>