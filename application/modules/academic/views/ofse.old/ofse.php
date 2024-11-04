<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary"><i class="fas fa-download"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">OFSE Filter</div>
            <div class="card-body">
                <?= modules::run('academic/ofse/form_filter_ofse') ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                OFSE Member List
                <div class="card-header-actions">
                    <a class="card-header-action" href="#" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                        <i class="fa fa-cog"></i> Quick Actions
                    </a>
                    <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                        <button type="button" id="download_subject_student_ofse" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download Student Data
                        </button>
                        <button type="button" id="download_subject_examiner" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download Subject and Examiner
                        </button>
                        <button type="button" id="download_ofse_registration_structure" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download Structure of OFSE Registration
                        </button>
                        <button type="button" id="download_ofse_result" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download OFSE Result
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= '' //modules::run('academic/ofse/view_ofse_lists_table') ?>
                <div class="table-responsive">
                    <table id="ofse_member_table" class="table table-bordered table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Study Program</th>
                                <th>Batch</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var ofse_member_table = $('table#ofse_member_table').DataTable({
    ajax : {
        url : '<?= base_url()?>academic/ofse/get_ofse_student',
        type : 'POST',
        // data : {
        //     academic_year_id: $('#academic_year_id').val(),
        //     semester_type_id: $('#semester_type_id').val(),
        //     study_program_id: $('#study_program_id').val()
        // },
        data: function(params) {
            let a_form_data = $('form#form_ofse_filter').serializeArray();
            var a_filter_data = objectify_form(a_form_data);
            return a_filter_data;
        }
    },
    columns: [
        {data: 'student_number'},
        {data: 'personal_data_name'},
        {data: 'study_program_abbreviation'},
        {data: 'student_batch'},
        {data: 'score_id'}
    ]
});

$(function() {
    $('button#filter_ofse').on('click', function(e) {
        e.preventDefault();

        ofse_member_table.ajax.reload(null, true);
    });

    $('button#download_subject_examiner').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        let url = '<?= base_url()?>academic/ofse/generate_ofse_csv';
        download_ofse_template(url);
    });

    $('button#download_subject_student_ofse').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        let url = '<?= base_url()?>academic/ofse/generate_ofse_student';
        download_ofse_template(url);
    });

    $('button#download_ofse_registration_structure').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        let url = '<?= base_url()?>academic/ofse/generate_ofse_registration_structure';
        download_ofse_template(url);
    });

    $('button#download_ofse_result').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        let url = '<?= base_url()?>academic/ofse/generate_ofse_result';
        download_ofse_template(url);
    });

    function download_ofse_template(url) {
        var data = $('form#form_ofse_filter').serialize();
        $.post(url , data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                window.location.href = '<?= base_url()?>file_manager/download_template/' + result.file;
            }else{
                toastr['error'](result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
        });
    }
});
</script>