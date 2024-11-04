<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Filter Data</div>
            <div class="card-body">
                <?= modules::run('krs/form_filter_krs_approval');?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Student Lists
                <div class="card-header-actions">
                    <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                        <i class="fas fa-sliders-h"></i> Quick Actions
                    </button>
                    <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                        <button id="btn_new_member_semester" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Student Class">
                            <i class="fas fa-clipboard-list"></i> Add Students
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= modules::run('krs/view_table_krs_student');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_krs_new_member">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Students to Semester Selected</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="table_list_students" class="table table-hover table-bordered">
                        <thead class="bg-dark">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Batch</th>
                                <th>Student Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_push_student_semester">Add to Lists</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="student_semester_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historical Semester of <span id="student_semester_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <form url="<?=base_url()?>krs/submit_student_semester" id="form_student_semester" onsubmit="return false">
                        <input type="hidden" name="student_id_semester" id="student_id_semester">
                        <table id="student_semester_history" class="table table-bordered table-hover">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Academic Semester</th>
                                    <th>Semester Number</th>
                                    <th>Status Study</th>
                                </tr>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit_student_semester">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
let table_list_students = $('#table_list_students').DataTable({
    processing: true,
    order: [[2, 'desc'],[1, 'asc']],
    ajax:{
        url: '<?=base_url()?>krs/get_student_not_in_semester',
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#form_filter_krs_approval').serializeArray();
            var a_filter_data = objectify_form(a_form_data);
            return a_filter_data;
        }
    },
    columns: [
        {data: 'student_number'},
        {data: 'personal_data_name'},
        {data: 'academic_year_id'},
        {
            data: 'student_status',
            render: function(data, type, rows) {
                return data.toUpperCase();
            }
        },
        {
            data: 'student_id',
            render: function(data, type, rows) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                if (rows.allowed_semester) {
                    html += '<button type="button" class="btn btn-info btn-sm" id="btn_add_semester">Add</button>';
                }
                html += '</div>';
                return html;
            }
        },
    ],
});
let student_semester_history = $('#student_semester_history').DataTable({
    processing: true,
    paging: false,
    searching: false,
    info: false,
    order: [[0, 'asc']],
    ajax:{
        url: '<?=base_url()?>krs/get_insert_list_student_semester',
        type: 'POST',
        data: function(d) {
            d.student_id = $('#student_id_semester').val();
            d.academic_year_id = $('#academic_year_id').val();
            d.semester_type_id = $('#semester_type_id').val();
        }
    },
    columns: [
        {data: 'student_id',
            render: function(data, type, row) {
                var html = '<input type="hidden" name="academic_year_id[]" value="' + row.academic_year_id + '">';
                html += '<input type="hidden" name="semester_type_id[]" value="' + row.semester_type_id + '">';
                html += row.academic_year_id + '' + row.semester_type_id;
                return html;
            }
        },
        {data: 'semester_number',
            render: function(data, type, row) {
                var selection = '<select name="student_semester_number[]" class="form-control">';
                for (let x = 1; x <= 14; x++) {
                    var selected = (x == data) ? 'selected="selected"' : '';
                    selection += '<option value="' + x + '" ' + selected + '>' + x + '</option>';
                }
                selection +='</select>';
                return selection;
            }
        },
        {data: 'student_semester_status'}
    ],
});
$(function() {
    $('#btn_new_member_semester').on('click', function(e) {
        e.preventDefault();
        
        table_list_students.ajax.reload();
        $("#modal_krs_new_member").modal('show');
    });

    $('#table_list_students tbody').on('click', 'button#btn_add_semester', function(e) {
        e.preventDefault();

        var student_data = table_list_students.row($(this).parents('tr')).data();
        $('#student_semester_name').html(student_data.personal_data_name);
        $("#student_id_semester").val(student_data.student_id);
        student_semester_history.ajax.reload();

        $("#modal_krs_new_member").modal('hide');
        $('#student_semester_modal').modal('show');
    });

    $('#submit_student_semester').on('click', function(e) {
        e.preventDefault();

        $.blockUI({baseZ: 9000});
        let form = $('#form_student_semester');
        let url = form.attr('url');
        var data = form.serialize();
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                $("#student_semester_modal").modal('hide');
                table_krs_student.ajax.reload();
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error');
        })
    })
})
</script>