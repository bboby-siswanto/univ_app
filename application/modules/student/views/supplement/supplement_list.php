<div class="card">
    <div class="card-header">
        Supplement Student
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="list_supplement" class="table table-border table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Study Program</th>
                        <th>Student Batch</th>
                        <th>Academic Semester</th>
                        <th>Description</th>
                        <th>Input By</th>
                        <th>Input Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
let list_supplement = $('#list_supplement').DataTable({
    ajax: {
        url: '<?= base_url()?>student/supplement/get_supplement_student',
        type: 'POST',
        // data: function(d){
        //     d.student_id = ''
        // }
    },
    order: [[7, 'desc']],
    columns: [
        {data: 'personal_data_name'},
        {data: 'student_number'},
        {data: 'study_program_abbreviation'},
        {data: 'student_batch'},
        {
            data: 'academic_year_id',
            render: function(data, type, row) {
                return data + row.semester_type_id;
            }
        },
        {
            data: 'supplement_comment',
            orderable: false
        },
        {
            data: 'added_by'
        },
        {
            data: 'added_date',
            render: function(data, type, row) {
                return '<span class="d-none">' + row.added_datestring + '</span>' + data;
            }
        },
        {
            data: 'supplement_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
                if (row['supplement_files']) {
                    html += '<button type="button" class="btn btn-info btn-sm dropdown-toggle" id="btn_download" data-toggle="dropdown"><i class="fas fa-file"></i></button>';
                    html += '<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">'
                        $.each(row['supplement_files'], function(i, v) {
                            html += '<a class="dropdown-item" href="<?=base_url()?>student/supplement/view_doc/' + v.supplement_doc_id + '" target="_blank">' + v.supplement_doc_fname + '</a>';
                        })
                    html += '</div>';
                }
                html += '</div>';
                console.log(html);
                return html;
            }
        },
    ]
})
</script>