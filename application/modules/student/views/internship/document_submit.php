<div class="card">
    <div class="card-header">
        Filter Data
    </div>
    <div class="card-body">
        <form id="form_internship_filter" onsubmit="return false">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="filter_study_program_id">Study Program</label>
                        <select name="filter_study_program_id" id="filter_study_program_id" class="form-control">
                            <option value=""></option>
                            <option value="all">All</option>
                    <?php
                    if ($study_program) {
                        foreach ($study_program as $o_study_program) {
                    ?>
                            <option value="<?=$o_study_program->study_program_id;?>"><?=$o_study_program->study_program_name;?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-info float-right" id="filter_data_form">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Student List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data_list" class="table table-border table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Batch</th>
                        <th>Study Program</th>
                        <th>Company Name</th>
                        <th>Department</th>
                        <th>Supervisor</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var data_list = $('#data_list').DataTable({
    ajax: {
        url: '<?=site_url('student/internship/get_student_list_submit')?>',
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#form_internship_filter').serialize();
            return a_form_data;
        }
    },
    columns: [
        {data: 'personal_data_name'},
        {data: 'student_number'},
        {data: 'academic_year_id'},
        {data: 'study_program_name'},
        {data: 'institution_name'},
        {data: 'department'},
        {data: 'supervisor_name'},
        {
            data: 'internship_id',
            orderable: false,
            render: function(data, type, row) {
                var file_list = '';
                var document_list = row.document_list;
                if (document_list) {
                    $.each(document_list, function(i, v) {
                        file_list += '<a class="dropdown-item" href="<?=base_url()?>student/internship/view_doc/' + data + '/' + v.document_type + '" target="_blank">' + v.document_type + '</a>';
                    })
                }
                var btn_files = '<div class="btn-group btn-group-sm" role="group">';
                btn_files += '<button id="btnGroupFiles" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-download"></i></button>'
                btn_files += '<div class="dropdown-menu" aria-labelledby="btnGroupFiles">';
                btn_files += file_list;
                btn_files += '</div></div>';
                html = '<div class="btn-group btn-group-sm">';
                html += btn_files;
                html += '</div>';
                return html;
            }
        },
    ]
});

$(function() {
    var filter_study_program_id = $('#filter_study_program_id').select2({
        allowClear: true,
        placeholder: 'Please select...',
        theme: "bootstrap"
    });

    $('#filter_data_form').on('click', function(e) {
        e.preventDefault();

        data_list.ajax.reload();
    });
})
</script>