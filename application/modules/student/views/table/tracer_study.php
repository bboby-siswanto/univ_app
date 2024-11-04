<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tracer_study" class="table table-responsive table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>No</th>
                        <th>Academic Year/Semester Type</th>
                        <th>Student Status</th>
                        <th>Gpa</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        var tracer_study_table = $('table#tracer_study').DataTable({
            ajax: {
                url: '<?=base_url()?>academic/student_academic/get_student_study',
                type: 'POST',
                data: {
                    student_id: '<?=$student_id;?>'
                }
            },
            columns: [
                {
                    data: 'student_id',
                    render: function ( data, type, row, meta ) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'semester_year_id',
                    render: function(data, type, row) {
                        return data + '' + row.semester_type_id
                    }
                },
                {
                    data: 'student_semester_status',
                    render: function(data, type, row) {
                        return data.toUpperCase();
                    }
                },
                {data: 'student_semester_gpa'},
                {
                    data: 'student_id',
                    render:  function(data, type, row) {
                        var html = '<div class="button-group">';
                        html += '<button type="button" id="change_status" class="btn btn-info btn-sm">Change Status</button>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });
    });
</script>