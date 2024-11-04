<div class="card">
    <div class="card-header">
        Filter Data
    </div>
    <div class="card-body">
        <form id="form_filter_accreditation" onsubmit="return false" url="<?=base_url()?>accreditation/get_list_lecturer_class">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="academic_year">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-control">
                            <option value=""></option>
                <?php
                if ($batch) {
                    foreach ($batch as $o_year) {
                ?>
                            <option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?>/<?= intval($o_year->academic_year_id)+1 ?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="study_program">Study Program</label>
                        <select name="study_program" id="study_program" class="form-control">
                            <option value=""></option>
                            <option value="all">All</option>
                <?php
                if ($study_program) {
                    foreach ($study_program as $o_prodi) {
                ?>
                            <option value="<?=$o_prodi->study_program_id;?>"><?=$o_prodi->study_program_name_feeder;?></option>
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
                    <button id="btn_filter_accreditation" class="btn btn-info float-right" type="button">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="alert alert-danger fade show" role="alert">
    <li>Mata Kuliah dengan kata <strong><i>Research Semester, Project Research, Research Project, Internship, Thesis, NFU</i></strong> tidak dihitung dalam total SKS</li>
    <li>Kelas dengan <strong><i>jumlah mahasiswa = 0</i></strong> tidak dihitung dalam total SKS</li>
    <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button> -->
</div>
<div class="card">
    <div class="card-header">
        List Lecturer
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="table_list_lecturer">
                <thead class="bg-dark">
                    <tr>
                        <th></th>
                        <th>Lecturer Reported</th>
                        <th>NIDN</th>
                        <th>Department</th>
                        <th>Total SKS Prodi</th>
                        <th>Total SKS Other Prodi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var table_list_lecturer = $('#table_list_lecturer').DataTable({
    processing: true,
    paging: false,
    ajax: {
        url: '<?= base_url()?>accreditation/get_list_lecturer_class',
        type: 'POST',
        // data: function(d){
        //     d.gsr_code = $('#gsr_code').val();
        //     d.gsr_daterange_start = $('#gsr_daterange_start').val();
        //     d.gsr_daterange_end = $('#gsr_daterange_end').val();
        //     d.account_no = $('#account_no').val();
        //     d.account_no = $('#account_no').val();
        // }
        data: function() {
            return $('#form_filter_accreditation').serialize();
        }
    },
    columns: [
        {
            className: 'dt-control',
            orderable: false,
            data: null,
            defaultContent: '<i class="fas fa-plus"></i>',
        },
        {
            data: 'personal_data_name'
        },
        { 
            data: 'employee_lecturer_number'
        },
        { 
            data: 'department_abbreviation'
        },
        { 
            data: 'total_sks_prodi'
        },
        { 
            data: 'total_sks_ot_prodi'
        },
        // {
        //     data: 'employee_id',
        //     orderable: false,
        //     render: function(data, type, row){
        //         var html = '<div class="btn-group">';
                
        //         html += '</div>';
        //         return html;
        //     }
        // }
    ],
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'excel',
            text: 'Download Excel',
            action: function ( e, dt, node, config ) {
                var s_academic_year = $('#academic_year').val();
                var s_prodi_id = $('#study_program').val();
                window.location.href = '<?=base_url()?>download/accreditation_download/download_lecturer_teach/' + s_academic_year + '/' + s_prodi_id;
            }
        }
    ],
});

$(function() {
    $('#btn_filter_accreditation').on('click', function(e) {
        table_list_lecturer.ajax.reload();
    });

    $('#academic_year, #study_program').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });

    $('#table_list_lecturer tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table_list_lecturer.row(tr);
 
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            $(this).html('<i class="fas fa-plus"></i>');
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            $(this).html('<i class="fas fa-minus"></i>');
            tr.addClass('shown');
        }
    });
});

function format(data) {
    var html = '';
    if (data.class_data) {
        var dataclass = data.class_data;
        html += '<table class="table">';
        html += '<tr class="bg-light">';
        html += '<td>Lecturer Class</td>';
        html += '<td>Academic Year</td>';
        html += '<td>Study Program</td>';
        html += '<td>Credit Allocation</td>';
        html += '<td>Subject</td>';
        html += '<td>Subject SKS</td>';
        html += '<td>Count Student</td>';
        html += '<td>Count Lecturer Absence</td>';
        html += '</tr>';
        $.each(dataclass, function(i, v) {
            html += '<tr class="' + ((v.this_calculated) ? '' : 'bg-danger') + '">';
            html += '<td>' + v.class_employee_name + '</td>';
            html += '<td>' + v.academic_year_id + ' ' + v.semester_type_name + '</td>';
            html += '<td>' + v.class_prodi + '</td>';
            html += '<td>' + v.credit_allocation + '</td>';
            html += '<td>' + v.subject_name + '</td>';
            html += '<td>' + v.curriculum_subject_credit + '</td>';
            html += '<td>' + v.class_student + '</td>';
            html += '<td>' + v.class_lecturer_absence + '</td>';
            html += '</tr>';
        })
        html += '</table>';
    }

    return (html);
}
</script>