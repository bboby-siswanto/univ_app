<div class="card">
    <div class="card-header">
        Filter Form
    </div>
    <div class="card-body">
        <form id="form_filter_period">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Period Academic Year</label>
                        <select name="period_academic_year" id="period_academic_year" class="form-control">
                            <option value=""></option>
                <?php
                if ((isset($active_batch)) AND ($active_batch)) {
                    foreach ($active_batch as $o_academic_year) {
                        $selected = ($o_academic_year->academic_year_id == $this->session->userdata('academic_year_id_active')) ? 'selected="selected"' : '';
                ?>
                            <option value="<?=$o_academic_year->academic_year_id;?>" <?=$selected;?>><?=$o_academic_year->academic_year_id;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Semester</label>
                        <select name="period_semester_type_id" id="period_semester_type_id" class="form-control">
                            <option value=""></option>
                <?php
                if ((isset($semester_type_list)) AND ($semester_type_list)) {
                    foreach ($semester_type_list as $p_semester_type) {
                        $selected = ($p_semester_type->semester_type_id == $this->session->userdata('semester_type_id_active')) ? 'selected="selected"' : '';
                ?>
                            <option value="<?=$p_semester_type->semester_type_id;?>" <?=$selected;?>><?=$p_semester_type->semester_type_name;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" id="submit_filter_period" name="submit_filter_period" class="btn btn-info float-right">Filter Period</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        List of Responden - Study Program
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="list_lecturer_table">
                <thead>
                    <tr class="bg-dark">
                        <th>Study Program</th>
                        <th>Number of Respondent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_detail_prodi">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List of Respondent Prodi <span id="prodi_detail_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="prodi_detail_student" class="table table-hover">
                    <thead class="bg-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Batch</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var table_list = $('#list_lecturer_table').DataTable({
    processing: true,
    ajax: {
        url: '<?=site_url('validation_requirement/lecturer_assesment/get_list_respondent_prodi')?>',
        data: function(d){
            d.period_academic_year = $('#period_academic_year').val();
            d.period_semester_type_id = $('#period_semester_type_id').val();
        },
        type: 'POST',
        // data: function(params) {
        //     let a_form_data = $('form#form_filter_period').serialize();
        //     // var a_filter_data = objectify_form(a_form_data);
        //     return a_form_data;
        // }
    },
    columns: [
        {data: 'study_program_name'},
        {data: 'count_student'},
        {
            data: 'score_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                html += '<button type="button" class="btn btn-info" id="btn_show_detail"><i class="fas fa-file-eye"></i>View Detail</button>';
                html += '</div>';
                return html;
            }
        },
    ]
});

var datalist = false;
var table_update = $('table#prodi_detail_student').DataTable({
    processing: true,
    data: datalist,
    columns: [
        {data: 'personal_data_name'},
        {data: 'student_batch'}
    ]
});

$(function() {
    $('select#period_academic_year, select#period_semester_type_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
    });

    $('#submit_filter_period').on('click', function(e) {
        e.preventDefault();

        table_list.ajax.reload();
    });

    $('#list_lecturer_table tbody').on('click', '#btn_show_detail', function(e) {
        e.preventDefault();

        var row_data = table_list.row($(this).parents('tr')).data();
        var list_student = row_data.list_student;

        table_update.clear();
        table_update.rows.add( list_student ).draw();

        $('#prodi_detail_name').text(row_data.study_program_name);
        $('#modal_detail_prodi').modal('show');
    })
});
</script>