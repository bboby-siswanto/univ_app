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
        List of Lecturer - Subject
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                <a href="<?=base_url()?>validation_requirement/lecturer_assesment/view_template" target="_blank" id="view_template_lecturer_assessment" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="View Template Lecturer Assessment">
                    <i class="fas fa-eye"></i> View Question Template
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="list_lecturer_table">
                <thead>
                    <tr class="bg-dark">
                        <th>Lecturer</th>
                        <th>Subject</th>
                        <th>Study Program</th>
                        <th>Count of Respondent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script>
var list_lecturer_table = $('table#list_lecturer_table').DataTable({
    processing: true,
    ajax: {
        url: '<?=site_url('validation_requirement/lecturer_assesment/get_list_lecturer_subject')?>',
        // data: function(d){
        //     d.academic_year_id = $('#period_academic_year').val();
        //     d.semester_type_id = $('#period_semester_type_id').val();
        // },
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#form_filter_period').serialize();
            // var a_filter_data = objectify_form(a_form_data);
            return a_form_data;
        }
    },
    columns: [
        {
            data: 'lecturer_name',
            render: function(data, type, row) {
                return data + ' / ' + row.employee_department;
            }
        },
        {data: 'subject_name'},
        {
            data: 'study_program',
            orderable: false,
            render: function(data, type, row) {
                return data.toUpperCase();
                // return row.class_master_id + '/' + row.employee_assessment;
            }
        },
        {data: 'counter_respondent'},
        {
            data: 'score_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                html += '<a class="btn btn-info" id="btn_show_result" href="<?=base_url()?>validation_requirement/lecturer_assesment/lecturer_result/' + row.class_master_id + '/' + row.employee_assessment + '" target="_blank"><i class="fas fa-file-alt"></i>View Result</a>';
                html += '<a class="btn btn-info" id="btn_prev_result" href="<?=base_url()?>validation_requirement/lecturer_assesment/lecturer_assessment_result/' + row.class_master_id + '/' + row.employee_assessment + '" target="_blank"><i class="fas fa-eye"></i>Download Result</a>';
                // html += '<button type="button" class="btn btn-info" id="btn_show_result"><i class="fas fa-file-alt"></i> View Result</button>';
                // html += '<button type="button" class="btn btn-info" id="btn_prev_result"><i class="fas fa-eye"></i> Preview Result</button>';
                html += '</div>';
                return html;
            }
        },
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

        list_lecturer_table.ajax.reload();
    })
})
</script>