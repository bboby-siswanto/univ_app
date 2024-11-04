<div class="table-responsive">
    <caption>Mandatory / Elective Subjects</caption>
    <table id="table_score" class="table table-bordered table-hover table-striped table-sm">
        <thead class="bg-dark">
            <tr>
                <th>No</th>
                <th>Academic Year</th>
                <th>Semester Type</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Q5</th>
                <th>Q6</th>
                <th>Quiz</th>
                <th>Final Exam</th>
                <th>Repetition Exam</th>
                <th>Final Score</th>
                <th>Absence</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>Credit / SKS</th>
                <th>Merit</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="18">Total</td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
            <tr>
                <td colspan="18">GPA</td>
                <td colspan="2" align="center" id="gpa_result">0</td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="table-responsive mt-5">
    <caption>Extracurricular Subject</caption>
    <table id="score_extracurricular" class="table table-bordered table-hover table-striped table-sm">
        <thead class="bg-dark">
            <tr>
                <th>No</th>
                <th>Academic Year</th>
                <th>Semester Type</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Q5</th>
                <th>Q6</th>
                <th>Quiz</th>
                <th>Final Exam</th>
                <th>Repetition Exam</th>
                <th>Final Score</th>
                <th>Absence</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>Credit / SKS</th>
                <th>Merit</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script>
    var view_score_ = function(data_filter, table_target) {
        if($.fn.DataTable.isDataTable('table#' + table_target)){
            $('table#' + table_target).DataTable().destroy();
        }

        $('table#' + table_target).DataTable({
            order: [[1, 'asc'],[2, 'asc'],[4, 'asc']],
            searching: false,
            info: false,
            paging: false,
            processing: true,
            // responsive: true,
            ajax: {
                url: '<?= base_url()?>academic/score/filter_score_student',
                type: 'POST',
                data: data_filter
            },
            columns: [
                {
                    data: 'score_id', responsivePriority: 1
                },
                {
                    data: 'academic_year_id',
                    visible: false,
                    render: function ( data, type, row, meta ) {
                        return '<span class="d-none">' + row.score_id + '</span>';
                    }
                },
                {
                    data: 'semester_type_id',
                    visible: false
                },
                {data: 'subject_code'},
                {data: 'subject_name', responsivePriority: 2},
                {data: 'score_quiz1'},
                {data: 'score_quiz2'},
                {data: 'score_quiz3'},
                {data: 'score_quiz4'},
                {data: 'score_quiz5'},
                {data: 'score_quiz6'},
                {data: 'score_quiz'},
                {
                    data: 'score_final_exam', responsivePriority: 8,
                    render: function ( data, type, row, meta ) {
                        return Number(data).toFixed(2);
                    }
                },
                {data: 'score_repetition_exam', responsivePriority: 9},                
                {data: 'score_sum', responsivePriority: 7, render: function(data, type, row) {
                    return Number(data).toFixed(2);
                }},
                {
                    data: 'score_absence',
                    render: function ( data, type, row, meta ) {
                        return Number(data).toFixed(2) + ' %';
                    }
                },
                {data: 'score_grade', responsivePriority: 5},
                {data: 'score_grade_point', responsivePriority: 6},
                {data: 'curriculum_subject_credit', responsivePriority: 3, render: function(data, type, row) {
                    if ($('select#filter_semester_type_id').val() == 4) {
                        return '0';
                    }else{
                        return data;
                    }
                }},
                {data: 'score_merit', responsivePriority: 4}
            ],
            columnDefs: [
            <?php
            if ($this->session->userdata('type') != 'staff') {  
            ?>
                {
                    targets: [5,6,7,8,9,10],
                    // targets: [3,4,5,6,7,8],
                    visible: false
                },
            <?php
            }  
            ?>
                {
                    targets: [5, 6, 7, 8, 9, 10, 11, 13, 14, -1],
                    // targets: [3, 4, 5, 6, 7, 8, 9, 11, 12, -1],
                    render: function(data, type, row) {
                        if (data != null) {
                            return Number(data).toFixed(2);
                        }else{
                            return data;
                        }
                    }
                },
                {
                    targets: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
                    orderable: false
                }
            ],
            footerCallback: function(row, data, start, end, display) {
                if (table_target != 'score_extracurricular') {
                    var api = this.api(), data;

                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    total_sks = api.column(-2, {page:'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    total_merit = api.column(-1, {page:'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    if ($('select#filter_semester_type_id').val() == 4) {
                        $(api.column(-2).footer()).html(0);
                        $(api.column(-1).footer()).html(0);
                        $('#gpa_result').html(0);
                    }else{
                        $(api.column(-2).footer()).html(total_sks);
                        $(api.column(-1).footer()).html(total_merit.toFixed(2));
                        var gpa_result = total_merit / total_sks;
                        $('#gpa_result').html(gpa_result.toFixed(2));
                    }
                }
            }
        });

        $('table#' + table_target).DataTable().on( 'order.dt search.dt', function () {
            $('table#' + table_target).DataTable().column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    }

    $(function() {
        view_score_({
            student_id: '<?= $student_id;?>', 
            academic_year_id: $('#filter_academic_year_id').val(), 
            semester_type_id: $('#filter_semester_type_id').val(),
        }, 'table_score');

        view_score_({
            student_id: '<?= $student_id;?>', 
            academic_year_id: $('#filter_academic_year_id').val(), 
            semester_type_id: $('#filter_semester_type_id').val(),
            curriculum_subject_type: 'extracurricular'
        }, 'score_extracurricular');

        $('button#filter_semester_score').on('click', function(e) {
            e.preventDefault();

            view_score_({
                student_id: '<?= $student_id;?>', 
                academic_year_id: $('#filter_academic_year_id').val(), 
                semester_type_id: $('#filter_semester_type_id').val(),
            }, 'table_score');

            view_score_({
                student_id: '<?= $student_id;?>', 
                academic_year_id: $('#filter_academic_year_id').val(), 
                semester_type_id: $('#filter_semester_type_id').val(),
                curriculum_subject_type: 'extracurricular'
            }, 'score_extracurricular');

            view_supplement({
                student_id: '<?= $student_id;?>', 
                academic_year_id: $('#filter_academic_year_id').val(), 
                semester_type_id: $('#filter_semester_type_id').val(),
            });
        });
    });
</script>